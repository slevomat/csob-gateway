<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

use DateTimeImmutable;
use SlevomatCsobGateway\Call\ResponseExtensionHandler;
use SlevomatCsobGateway\Crypto\CryptoService;
use SlevomatCsobGateway\Crypto\PrivateKeyFileException;
use SlevomatCsobGateway\Crypto\PublicKeyFileException;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Crypto\SigningFailedException;
use SlevomatCsobGateway\Crypto\VerificationFailedException;

class ApiClient
{

	/**
	 * @var ApiClientDriver
	 */
	private $driver;

	/**
	 * @var CryptoService
	 */
	private $cryptoService;

	/**
	 * @var string
	 */
	private $apiUrl;

	public function __construct(
		ApiClientDriver $driver,
		CryptoService $cryptoService,
		string $apiUrl = null
	)
	{
		$this->driver = $driver;
		$this->cryptoService = $cryptoService;
		$this->apiUrl = $apiUrl;
	}

	/**
	 * @param string $url
	 * @param mixed[]|null $data
	 * @param SignatureDataFormatter $requestSignatureDataFormatter
	 * @param SignatureDataFormatter $responseSignatureDataFormatter
	 * @param \Closure|null $responseValidityCallback
	 * @param ResponseExtensionHandler[] $extensions
	 * @return Response
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
		array $data = [],
		SignatureDataFormatter $requestSignatureDataFormatter,
		SignatureDataFormatter $responseSignatureDataFormatter,
		\Closure $responseValidityCallback = null,
		array $extensions = []
	): Response
	{
		return $this->request(
			new HttpMethod(HttpMethod::GET),
			$url,
			$this->prepareData($data, $requestSignatureDataFormatter),
			null,
			$responseSignatureDataFormatter,
			$responseValidityCallback,
			$extensions
		);
	}

	/**
	 * @param string $url
	 * @param mixed[]|null $data
	 * @param SignatureDataFormatter $requestSignatureDataFormatter
	 * @param SignatureDataFormatter $responseSignatureDataFormatter
	 * @param ResponseExtensionHandler[] $extensions
	 * @return Response
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
		array $data = [],
		SignatureDataFormatter $requestSignatureDataFormatter,
		SignatureDataFormatter $responseSignatureDataFormatter,
		array $extensions = []
	): Response
	{
		return $this->request(
			new HttpMethod(HttpMethod::POST),
			$url,
			[],
			$this->prepareData($data, $requestSignatureDataFormatter),
			$responseSignatureDataFormatter,
			null,
			$extensions
		);
	}

	/**
	 * @param string $url
	 * @param mixed[]|null $data
	 * @param SignatureDataFormatter $requestSignatureDataFormatter
	 * @param SignatureDataFormatter $responseSignatureDataFormatter
	 * @param ResponseExtensionHandler[] $extensions
	 * @return Response
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
		array $data = [],
		SignatureDataFormatter $requestSignatureDataFormatter,
		SignatureDataFormatter $responseSignatureDataFormatter,
		array $extensions = []
	): Response
	{
		return $this->request(
			new HttpMethod(HttpMethod::PUT),
			$url,
			[],
			$this->prepareData($data, $requestSignatureDataFormatter),
			$responseSignatureDataFormatter,
			null,
			$extensions
		);
	}

	/**
	 * @param HttpMethod $method
	 * @param string $url
	 * @param string[] $queries
	 * @param mixed[]|null $data
	 * @param SignatureDataFormatter $responseSignatureDataFormatter
	 * @param \Closure|null $responseValidityCallback
	 * @param ResponseExtensionHandler[] $extensions
	 * @return Response
	 *
	 * @throws PrivateKeyFileException
	 * @throws SigningFailedException
	 * @throws PublicKeyFileException
	 * @throws VerificationFailedException
	 * @throws RequestException
	 * @throws ApiClientDriverException
	 * @throws InvalidSignatureException
	 */
	public function request(
		HttpMethod $method,
		string $url,
		array $queries = [],
		array $data = null,
		SignatureDataFormatter $responseSignatureDataFormatter,
		\Closure $responseValidityCallback = null,
		array $extensions = []
	): Response
	{
		foreach ($queries as $key => $value) {
			if (strpos($url, '{' . $key . '}') !== false) {
				$url = str_replace('{' . $key . '}', urlencode((string) $value), $url);
				unset($queries[$key]);
			}
		}

		if ($queries !== []) {
			throw new \InvalidArgumentException('Arguments are missing URL placeholders: ' . json_encode($queries));
		}

		$response = $this->driver->request(
			$method,
			$this->apiUrl . '/' . $url,
			$data
		);

		if ($responseValidityCallback !== null) {
			$responseValidityCallback($response);
		}

		if ($response->getResponseCode()->equalsValue(ResponseCode::S200_OK)) {
			$decodedExtensions = [];
			if ($extensions !== [] && array_key_exists('extensions', $response->getData())) {
				foreach ($response->getData()['extensions'] as $extensionData) {
					$name = $extensionData['extension'];
					if (isset($extensions[$name])) {
						$handler = $extensions[$name];
						$decodedExtensions[$name] = $handler->createResponse($this->decodeData($extensionData, $handler->getSignatureDataFormatter()));
					}
				}
			}
			$responseData = $this->decodeData($response->getData(), $responseSignatureDataFormatter);
			unset($responseData['extensions']);

			return new Response(
				$response->getResponseCode(),
				$responseData,
				$response->getHeaders(),
				$decodedExtensions
			);

		} elseif ($response->getResponseCode()->equalsValue(ResponseCode::S303_SEE_OTHER)) {
			return new Response(
				$response->getResponseCode(),
				null,
				$response->getHeaders()
			);

		} elseif ($response->getResponseCode()->equalsValue(ResponseCode::S400_BAD_REQUEST)) {
			throw new BadRequestException($response);

		} elseif ($response->getResponseCode()->equalsValue(ResponseCode::S403_FORBIDDEN)) {
			throw new ForbiddenException($response);

		} elseif ($response->getResponseCode()->equalsValue(ResponseCode::S404_NOT_FOUND)) {
			throw new NotFoundException($response);

		} elseif ($response->getResponseCode()->equalsValue(ResponseCode::S405_METHOD_NOT_ALLOWED)) {
			throw new MethodNotAllowedException($response);

		} elseif ($response->getResponseCode()->equalsValue(ResponseCode::S429_TOO_MANY_REQUESTS)) {
			throw new TooManyRequestsException($response);

		} elseif ($response->getResponseCode()->equalsValue(ResponseCode::S503_SERVICE_UNAVAILABLE)) {
			throw new ServiceUnavailableException($response);
		}

		throw new InternalErrorException($response);
	}

	/**
	 * @param mixed[] $data
	 * @param SignatureDataFormatter $responseSignatureDataFormatter
	 * @return Response
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
			new ResponseCode(ResponseCode::S200_OK),
			$data
		);

		return new Response(
			$response->getResponseCode(),
			$this->decodeData($data, $responseSignatureDataFormatter),
			$response->getHeaders()
		);
	}

	/**
	 * @param mixed[] $data
	 * @param SignatureDataFormatter $signatureDataFormatter
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
	 * @param SignatureDataFormatter $signatureDataFormatter
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

}
