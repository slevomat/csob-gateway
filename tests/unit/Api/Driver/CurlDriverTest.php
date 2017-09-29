<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api\Driver;

use SlevomatCsobGateway\Api\ApiClientDriverException;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class CurlDriverTest extends \PHPUnit\Framework\TestCase
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

		$this->assertInstanceOf(Response::class, $response);
		$this->assertSame(ResponseCode::S200_OK, $response->getResponseCode()->getValue());
		$this->assertEquals([
			'text' => 'foo text',
		], $response->getData());
		$this->assertEquals([
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
			$this->assertInstanceOf(ApiClientDriverException::class, $e);
			$this->assertSame(11, $e->getCode());
			$this->assertSame('foo getinfo', $e->getInfo());
		}
	}

}
