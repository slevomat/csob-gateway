<?php declare(strict_types = 1);

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
	}

	/**
	 * @param HttpMethod $method
	 * @param string $url
	 * @param mixed[]|null $data
	 * @param string[] $headers
	 * @return Response
	 * @throws GuzzleDriverException
	 */
	public function request(HttpMethod $method, string $url, ?array $data, array $headers = []): Response
	{
		$postData = null;
		if ($method->equalsValue(HttpMethod::POST) || $method->equalsValue(HttpMethod::PUT)) {
			$postData = json_encode($data);
		}
		$headers += ['Content-Type' => 'application/json'];
		$request = new Request($method->getValue(), $url, $headers, $postData);

		try {
			$httpResponse = $this->client->send($request, [
				RequestOptions::HTTP_ERRORS => false,
				RequestOptions::ALLOW_REDIRECTS => false,
			]);

			$responseCode = ResponseCode::get($httpResponse->getStatusCode());

			$responseHeaders = array_map(function ($item) {
				return !is_array($item) || count($item) > 1 ? $item : array_shift($item);
			}, $httpResponse->getHeaders());

			return new Response(
				$responseCode,
				json_decode((string) $httpResponse->getBody(), true),
				$responseHeaders
			);
		} catch (\Throwable $e) {
			throw new GuzzleDriverException($e);
		}
	}

}
