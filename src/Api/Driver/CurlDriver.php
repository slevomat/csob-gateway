<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api\Driver;

use ErrorException;
use SlevomatCsobGateway\Api\ApiClientDriver;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use const CURLINFO_HEADER_SIZE;
use const CURLINFO_HTTP_CODE;
use const CURLOPT_COOKIESESSION;
use const CURLOPT_CUSTOMREQUEST;
use const CURLOPT_FOLLOWLOCATION;
use const CURLOPT_HEADER;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_SSL_VERIFYPEER;
use const CURLOPT_TIMEOUT;
use function explode;
use function json_decode;
use function json_encode;
use function substr;
use function trim;

class CurlDriver implements ApiClientDriver
{

	/** @var int */
	private $timeout = 20;

	/**
	 * @param HttpMethod $method
	 * @param string $url
	 * @param mixed[]|null $data
	 * @param string[] $headers
	 * @return Response
	 *
	 * @throws CurlDriverException
	 */
	public function request(HttpMethod $method, string $url, ?array $data, array $headers = []): Response
	{
		$ch = curl_init($url);

		if ($ch === false) {
			throw new ErrorException('Failed to initialize curl resource.');
		}

		if ($method->equalsValue(HttpMethod::POST) || $method->equalsValue(HttpMethod::PUT)) {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method->getValue());
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		}

		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_COOKIESESSION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers + [
			'Content-Type: application/json',
		]);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		$output = curl_exec($ch);

		if ($output === false) {
			throw new CurlDriverException($ch);
		}

		$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headers = substr((string) $output, 0, $headerSize);
		$body = substr((string) $output, $headerSize);

		$responseCode = ResponseCode::get(curl_getinfo($ch, CURLINFO_HTTP_CODE));

		curl_close($ch);

		return new Response(
			$responseCode,
			json_decode($body, true),
			$this->parseHeaders($headers)
		);
	}

	/**
	 * @param string $rawHeaders
	 * @return string[]
	 */
	private function parseHeaders(string $rawHeaders): array
	{
		$headers = [];

		foreach (explode("\n", $rawHeaders) as $line) {
			$line = explode(':', $line, 2);

			if (!isset($line[1])) {
				continue;
			}

			$headers[$line[0]] = trim($line[1]);
		}

		return $headers;
	}

}
