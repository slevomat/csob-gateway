<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api\Driver;

use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClientDriverException;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class CurlDriverTest extends TestCase
{

	/**
	 * @runInSeparateProcess
	 */
	public function testRequest(): void
	{
		include __DIR__ . '/CurlMock.php';

		$curlDriver = new CurlDriver();

		$response = $curlDriver->request(
			HttpMethod::get(HttpMethod::POST),
			'foo/url',
			null,
			[
				'Content-Type' => 'application/json',
			]
		);

		self::assertInstanceOf(Response::class, $response);
		self::assertSame(ResponseCode::S200_OK, $response->getResponseCode()->getValue());
		self::assertEquals([
			'text' => 'foo text',
		], $response->getData());
		self::assertEquals([
			'abc' => 'def',
		], $response->getHeaders());
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testCurlDriverException(): void
	{
		include __DIR__ . '/Curl_exec_false_Mock.php';

		$curlDriver = new CurlDriver();

		try {
			$curlDriver->request(
				HttpMethod::get(HttpMethod::POST),
				'foo/url',
				null,
				[
					'Content-Type' => 'application/json',
				]
			);

		} catch (CurlDriverException $e) {
			self::assertInstanceOf(ApiClientDriverException::class, $e);
			self::assertSame(11, $e->getCode());
			self::assertSame('foo getinfo', $e->getInfo());
		}
	}

}
