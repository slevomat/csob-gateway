<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class EchoRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('get')
			->with('echo/{merchantId}/{dttm}/{signature}', [
				'merchantId' => '012345',
			])
			->willReturn(
				new Response(ResponseCode::S200_OK, [
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
				]),
			);

		$echoRequest = new EchoRequest(
			'012345',
		);

		$echoResponse = $echoRequest->send($apiClient);

		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $echoResponse->getResponseDateTime());
		self::assertEquals(ResultCode::C0_OK, $echoResponse->getResultCode());
		self::assertSame('OK', $echoResponse->getResultMessage());
	}

}
