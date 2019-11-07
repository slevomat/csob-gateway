<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class PostEchoRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('echo', [
				'merchantId' => '012345',
			])
			->willReturn(
				new Response(ResponseCode::get(ResponseCode::S200_OK), [
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
				])
			);

		/** @var ApiClient $apiClient */
		$echoRequest = new PostEchoRequest(
			'012345'
		);

		$echoResponse = $echoRequest->send($apiClient);

		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $echoResponse->getResponseDateTime());
		self::assertEquals(ResultCode::get(ResultCode::C0_OK), $echoResponse->getResultCode());
		self::assertSame('OK', $echoResponse->getResultMessage());
	}

}
