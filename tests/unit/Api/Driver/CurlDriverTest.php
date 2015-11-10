<?php

namespace SlevomatCsobGateway\Api\Driver;

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
			[
				'fooQuery' => 123,
			],
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

}
