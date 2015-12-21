<?php

namespace SlevomatCsobGateway\Api\Driver;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use SlevomatCsobGateway\Api\ApiClientDriver;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class GuzzleDriver implements ApiClientDriver
{

	/** @var Client */
	private $client;

	public function __construct(Client $client)
	{
		$this->client = $client;
		$this->validateClientConfig();
	}

	/**
	 * @param HttpMethod $method
	 * @param string $url
	 * @param mixed[]|null $data
	 * @param string[] $headers
	 * @return Response
	 */
	public function request(HttpMethod $method, $url, array $data = null, array $headers = [])
	{
		$postData = null;
		if ($method->equalsValue(HttpMethod::POST) || $method->equalsValue(HttpMethod::PUT)) {
			$postData = json_encode($data);
		}
		$headers += ['Content-Type' => 'application/json'];
		$request = new Request($method->getValue(), $url, $headers, $postData);

		try {
			$httpResponse = $this->client->send($request);

			$responseCode = new ResponseCode($httpResponse->getStatusCode());

			$responseHeaders = array_map(function ($item) {
				return !is_array($item) || count($item) > 1 ? $item : array_shift($item);
			}, $httpResponse->getHeaders());

			return new Response(
				$responseCode,
				json_decode($httpResponse->getBody(), JSON_OBJECT_AS_ARRAY),
				$responseHeaders
			);
		} catch (\Exception $e) {
			throw new GuzzleDriverException($e);
		}
	}

	private function validateClientConfig()
	{
		$options = $this->client->getConfig();
		if ($options[RequestOptions::ALLOW_REDIRECTS] !== false) {
			throw new InvalidGuzzleConfigurationException(sprintf(
				'Guzzle HTTP client has to be configured to not follow redirect. Set option %s to false.',
				RequestOptions::ALLOW_REDIRECTS
			));
		}

		if ($options[RequestOptions::HTTP_ERRORS] !== false) {
			throw new InvalidGuzzleConfigurationException(sprintf(
				'Guzzle HTTP client has to be configured not to throw exceptions on non-OK response code. Set config option %s to false.',
				RequestOptions::HTTP_ERRORS
			));
		}

		if ($options[RequestOptions::VERIFY] === false) {
			throw new InvalidGuzzleConfigurationException(sprintf(
				'Setting %s config option to false is insecure and therefore not supported. Set option to true or to provide custom CA bundle file.',
				RequestOptions::VERIFY
			));
		}
	}

}
