<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api\Driver;

use SlevomatCsobGateway\Api\ApiClientDriverException;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class CurlDriverTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @runInSeparateProcess
	 */
	public function testRequest()
	{
		include __DIR__ . '/CurlMock.php';

		$curlDriver = new CurlDriver();

		$response = $curlDriver->request(
			new HttpMethod(HttpMethod::POST),
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
	public function testCurlDriverException()
	{
		include __DIR__ . '/Curl_exec_false_Mock.php';

		$curlDriver = new CurlDriver();

		try {
			$curlDriver->request(
				new HttpMethod(HttpMethod::POST),
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
