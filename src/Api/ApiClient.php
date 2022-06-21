<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

use Closure;
use DateTimeImmutable;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use SlevomatCsobGateway\Call\ResponseExtensionHandler;
use SlevomatCsobGateway\Crypto\CryptoService;
use SlevomatCsobGateway\Crypto\PrivateKeyFileException;
use SlevomatCsobGateway\Crypto\PublicKeyFileException;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Crypto\SigningFailedException;
use SlevomatCsobGateway\Crypto\VerificationFailedException;
use function array_key_exists;
use function json_encode;
use function microtime;
use function str_replace;
use function strpos;
use function substr;
use function urlencode;

class ApiClient
{

	private ?LoggerInterface $logger = null;

	private ?string $apiUrl = null;

	public function __construct(
		private ApiClientDriver $driver,
		private CryptoService $cryptoService,
		string $apiUrl,
	)
	{
		$this->apiUrl = $apiUrl;
	}

	public function setLogger(?LoggerInterface $logger): void
	{
		$this->logger = $logger;
	}

	/**
	 * @param mixed[] $data
	 * @param ResponseExtensionHandler[] $extensions
	 *
	 * @throws PrivateKeyFileException
	 * @throws SigningFailedException
	 * @throws PublicKeyFileException
	 * @throws VerificationFailedException
	 * @throws RequestException
	 * @throws ApiClientDriverException
	 * @throws InvalidSignatureException
	 */
	public function get(
		string $url,
		array $data,
		SignatureDataFormatter $requestSignatureDataFormatter,
		SignatureDataFormatter $responseSignatureDataFormatter,
		?Closure $responseValidityCallback = null,
		array $extensions = [],
	): Response
	{
		return $this->request(
			HttpMethod::get(HttpMethod::GET),
			$url,
			$this->prepareData($data, $requestSignatureDataFormatter),
			null,
			$responseSignatureDataFormatter,
			$responseValidityCallback,
			$extensions,
		);
	}

	/**
	 * @param mixed[] $data
	 * @param ResponseExtensionHandler[] $extensions
	 *
	 * @throws PrivateKeyFileException
	 * @throws SigningFailedException
	 * @throws PublicKeyFileException
	 * @throws VerificationFailedException
	 * @throws RequestException
	 * @throws ApiClientDriverException
	 * @throws InvalidSignatureException
	 */
	public function post(
		string $url,
		array $data,
		SignatureDataFormatter $requestSignatureDataFormatter,
		SignatureDataFormatter $responseSignatureDataFormatter,
		array $extensions = [],
	): Response
	{
		return $this->request(
			HttpMethod::get(HttpMethod::POST),
			$url,
			[],
			$this->prepareData($data, $requestSignatureDataFormatter),
			$responseSignatureDataFormatter,
			null,
			$extensions,
		);
	}

	/**
	 * @param mixed[] $data
	 * @param ResponseExtensionHandler[] $extensions
	 *
	 * @throws PrivateKeyFileException
	 * @throws SigningFailedException
	 * @throws PublicKeyFileException
	 * @throws VerificationFailedException
	 * @throws RequestException
	 * @throws ApiClientDriverException
	 * @throws InvalidSignatureException
	 */
	public function put(
		string $url,
		array $data,
		SignatureDataFormatter $requestSignatureDataFormatter,
		SignatureDataFormatter $responseSignatureDataFormatter,
		array $extensions = [],
	): Response
	{
		return $this->request(
			HttpMethod::get(HttpMethod::PUT),
			$url,
			[],
			$this->prepareData($data, $requestSignatureDataFormatter),
			$responseSignatureDataFormatter,
			null,
			$extensions,
		);
	}

	/**
	 * @param mixed[] $queries
	 * @param mixed[]|null $data
	 * @param ResponseExtensionHandler[] $extensions
	 *
	 * @throws PrivateKeyFileException
	 * @throws SigningFailedException
	 * @throws PublicKeyFileException
	 * @throws VerificationFailedException
	 * @throws RequestException
	 * @throws ApiClientDriverException
	 * @throws InvalidSignatureException
	 */
	private function request(
		HttpMethod $method,
		string $url,
		array $queries,
		?array $data,
		SignatureDataFormatter $responseSignatureDataFormatter,
		?Closure $responseValidityCallback,
		array $extensions = [],
	): Response
	{
		$urlFirstQueryPosition = strpos($url, '{');
		$endpointName = $urlFirstQueryPosition !== false ? substr($url, 0, $urlFirstQueryPosition - 1) : $url;
		$originalQueries = $queries;

		foreach ($queries as $key => $value) {
			if (strpos($url, '{' . $key . '}') === false) {
				continue;
			}

			$url = str_replace('{' . $key . '}', urlencode((string) $value), $url);
			unset($queries[$key]);
		}

		if ($queries !== []) {
			throw new InvalidArgumentException('Arguments are missing URL placeholders: ' . json_encode($queries));
		}

		$requestStartTime = microtime(true);

		$response = $this->driver->request(
			$method,
			$this->apiUrl . '/' . $url,
			$data,
		);

		$this->logRequest($method, $endpointName, $originalQueries, $data, $response, microtime(true) - $requestStartTime);

		if ($responseValidityCallback !== null) {
			$responseValidityCallback($response);
		}

		if ($response->getResponseCode()->equalsValue(ResponseCode::S200_OK)) {
			$decodedExtensions = [];
			/** @var mixed[]|null $responseData */
			$responseData = $response->getData();
			if ($extensions !== [] && $responseData !== null && array_key_exists('extensions', $responseData)) {
				foreach ($responseData['extensions'] as $extensionData) {
					$name = $extensionData['extension'];
					if (!isset($extensions[$name])) {
						continue;
					}

					$handler = $extensions[$name];
					$decodedExtensions[$name] = $handler->createResponse($this->decodeData($extensionData, $handler->getSignatureDataFormatter()));
				}
			}
			$responseData = $this->decodeData($responseData ?? [], $responseSignatureDataFormatter);
			unset($responseData['extensions']);

			return new Response(
				$response->getResponseCode(),
				$responseData,
				$response->getHeaders(),
				$decodedExtensions,
			);

		}
		if ($response->getResponseCode()->equalsValue(ResponseCode::S303_SEE_OTHER)) {
			return new Response(
				$response->getResponseCode(),
				null,
				$response->getHeaders(),
			);

		}
		if ($response->getResponseCode()->equalsValue(ResponseCode::S400_BAD_REQUEST)) {
			throw new BadRequestException($response);
		}
		if ($response->getResponseCode()->equalsValue(ResponseCode::S403_FORBIDDEN)) {
			throw new ForbiddenException($response);
		}
		if ($response->getResponseCode()->equalsValue(ResponseCode::S404_NOT_FOUND)) {
			throw new NotFoundException($response);
		}
		if ($response->getResponseCode()->equalsValue(ResponseCode::S405_METHOD_NOT_ALLOWED)) {
			throw new MethodNotAllowedException($response);
		}
		if ($response->getResponseCode()->equalsValue(ResponseCode::S429_TOO_MANY_REQUESTS)) {
			throw new TooManyRequestsException($response);
		}
		if ($response->getResponseCode()->equalsValue(ResponseCode::S503_SERVICE_UNAVAILABLE)) {
			throw new ServiceUnavailableException($response);
		}

		throw new InternalErrorException($response);
	}

	/**
	 * @param mixed[] $data
	 *
	 * @throws InvalidSignatureException
	 * @throws PrivateKeyFileException
	 * @throws SigningFailedException
	 * @throws PublicKeyFileException
	 * @throws VerificationFailedException
	 */
	public function createResponseByData(array $data, SignatureDataFormatter $responseSignatureDataFormatter): Response
	{
		$response = new Response(
			ResponseCode::get(ResponseCode::S200_OK),
			$data,
		);

		$this->logRequest(HttpMethod::get(HttpMethod::GET), 'payment/response', [], [], $response, 0.0);

		return new Response(
			$response->getResponseCode(),
			$this->decodeData($data, $responseSignatureDataFormatter),
			$response->getHeaders(),
		);
	}

	/**
	 * @param mixed[] $data
	 * @return mixed[]
	 *
	 * @throws PrivateKeyFileException
	 * @throws SigningFailedException
	 */
	private function prepareData(array $data, SignatureDataFormatter $signatureDataFormatter): array
	{
		$data['dttm'] = (new DateTimeImmutable())->format('YmdHis');
		$data['signature'] = $this->cryptoService->signData($data, $signatureDataFormatter);

		return $data;
	}

	/**
	 * @param mixed[] $responseData
	 * @return mixed[]
	 *
	 * @throws InvalidSignatureException
	 * @throws PublicKeyFileException
	 * @throws VerificationFailedException
	 */
	private function decodeData(array $responseData, SignatureDataFormatter $signatureDataFormatter): array
	{
		if (!array_key_exists('signature', $responseData)) {
			throw new InvalidSignatureException($responseData);
		}

		$signature = $responseData['signature'];
		unset($responseData['signature']);

		if (!$this->cryptoService->verifyData($responseData, $signature, $signatureDataFormatter)) {
			throw new InvalidSignatureException($responseData);
		}

		return $responseData;
	}

	/**
	 * @param mixed[] $queries
	 * @param mixed[]|null $requestData
	 */
	private function logRequest(HttpMethod $method, string $url, array $queries, ?array $requestData, Response $response, float $responseTime): void
	{
		if ($this->logger === null) {
			return;
		}

		$responseData = $response->getData();

		unset($requestData['signature']);
		unset($queries['signature']);
		unset($responseData['signature']);

		if (isset($responseData['extensions'])) {
			foreach ($responseData['extensions'] as $key => $extensionData) {
				unset($responseData['extensions'][$key]['signature']);
			}
		}
		$context = [
			'request' => [
				'method' => $method->getValue(),
				'queries' => $queries,
				'data' => $requestData,
			],
			'response' => [
				'code' => $response->getResponseCode()->getValue(),
				'data' => $responseData,
				'headers' => $response->getHeaders(),
				'time' => $responseTime,
			],
		];

		$this->logger->info($url, $context);
	}

}
