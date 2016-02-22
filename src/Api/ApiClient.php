<?php

namespace SlevomatCsobGateway\Api;

use DateTimeImmutable;
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

	/**
	 * @param ApiClientDriver $driver
	 * @param CryptoService $cryptoService
	 * @param string|null $apiUrl
	 */
	public function __construct(
		ApiClientDriver $driver,
		CryptoService $cryptoService,
		$apiUrl = null
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
	 * @return Response
	 *
	 * @throws PrivateKeyFileException
	 * @throws SigningFailedException
	 * @throws PublicKeyFileException
	 * @throws VerificationFailedException
	 * @throws RequestException
	 * @throws ApiClientDriverException
	 */
	public function get(
		$url,
		array $data = [],
		SignatureDataFormatter $requestSignatureDataFormatter,
		SignatureDataFormatter $responseSignatureDataFormatter,
		\Closure $responseValidityCallback = null
	)
	{
		return $this->request(
			new HttpMethod(HttpMethod::GET),
			$url,
			$this->prepareData($data, $requestSignatureDataFormatter),
			null,
			$responseSignatureDataFormatter,
			$responseValidityCallback
		);
	}

	/**
	 * @param string $url
	 * @param mixed[]|null $data
	 * @param SignatureDataFormatter $requestSignatureDataFormatter
	 * @param SignatureDataFormatter $responseSignatureDataFormatter
	 * @return Response
	 *
	 * @throws PrivateKeyFileException
	 * @throws SigningFailedException
	 * @throws PublicKeyFileException
	 * @throws VerificationFailedException
	 * @throws RequestException
	 * @throws ApiClientDriverException
	 */
	public function post(
		$url,
		array $data = [],
		SignatureDataFormatter $requestSignatureDataFormatter,
		SignatureDataFormatter $responseSignatureDataFormatter
	)
	{
		return $this->request(
			new HttpMethod(HttpMethod::POST),
			$url,
			[],
			$this->prepareData($data, $requestSignatureDataFormatter),
			$responseSignatureDataFormatter
		);
	}

	/**
	 * @param string $url
	 * @param mixed[]|null $data
	 * @param SignatureDataFormatter $requestSignatureDataFormatter
	 * @param SignatureDataFormatter $responseSignatureDataFormatter
	 * @return Response
	 *
	 * @throws PrivateKeyFileException
	 * @throws SigningFailedException
	 * @throws PublicKeyFileException
	 * @throws VerificationFailedException
	 * @throws RequestException
	 * @throws ApiClientDriverException
	 */
	public function put(
		$url,
		array $data = [],
		SignatureDataFormatter $requestSignatureDataFormatter,
		SignatureDataFormatter $responseSignatureDataFormatter
	)
	{
		return $this->request(
			new HttpMethod(HttpMethod::PUT),
			$url,
			[],
			$this->prepareData($data, $requestSignatureDataFormatter),
			$responseSignatureDataFormatter
		);
	}

	/**
	 * @param HttpMethod $method
	 * @param string $url
	 * @param string[] $queries
	 * @param mixed[]|null $data
	 * @param SignatureDataFormatter $responseSignatureDataFormatter
	 * @param \Closure|null $responseValidityCallback
	 * @return Response
	 *
	 * @throws PrivateKeyFileException
	 * @throws SigningFailedException
	 * @throws PublicKeyFileException
	 * @throws VerificationFailedException
	 * @throws RequestException
	 * @throws ApiClientDriverException
	 */
	public function request(
		HttpMethod $method,
		$url,
		array $queries = [],
		array $data = null,
		SignatureDataFormatter $responseSignatureDataFormatter,
		\Closure $responseValidityCallback = null
	)
	{
		foreach ($queries as $key => $value) {
			if (strpos($url, '{' . $key . '}') !== false) {
				$url = str_replace('{' . $key . '}', urlencode($value), $url);
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
			return new Response(
				$response->getResponseCode(),
				$this->decodeData($response, $responseSignatureDataFormatter),
				$response->getHeaders()
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
	public function createResponseByData(array $data, SignatureDataFormatter $responseSignatureDataFormatter)
	{
		$response = new Response(
			new ResponseCode(ResponseCode::S200_OK),
			$data
		);

		return new Response(
			$response->getResponseCode(),
			$this->decodeData($response, $responseSignatureDataFormatter),
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
	private function prepareData(array $data, SignatureDataFormatter $signatureDataFormatter)
	{
		$data['dttm'] = (new DateTimeImmutable())->format('YmdHis');
		$data['signature'] = $this->cryptoService->signData($data, $signatureDataFormatter);

		return $data;
	}

	/**
	 * @param Response $response
	 * @param SignatureDataFormatter $signatureDataFormatter
	 * @return mixed[]
	 *
	 * @throws InvalidSignatureException
	 * @throws PublicKeyFileException
	 * @throws VerificationFailedException
	 */
	private function decodeData(Response $response, SignatureDataFormatter $signatureDataFormatter)
	{
		$data = $response->getData();

		if (!array_key_exists('signature', $data)) {
			throw new InvalidSignatureException($response);
		}

		$signature = $data['signature'];
		unset($data['signature']);

		if (!$this->cryptoService->verifyData($data, $signature, $signatureDataFormatter)) {
			throw new InvalidSignatureException($response);
		}

		return $data;
	}

}
