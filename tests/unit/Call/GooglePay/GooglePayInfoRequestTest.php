<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\GooglePay;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Call\ResultCode;

class GooglePayInfoRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('googlepay/info', [
				'merchantId' => '012345',
			])
			->willReturn(
				new Response(ResponseCode::get(ResponseCode::S200_OK), [
					'dttm' => '20190425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'checkoutParams' => ['foo' => 'bar'],
				])
			);

		$googlePayInfoRequest = new GooglePayInfoRequest('012345');

		$paymentResponse = $googlePayInfoRequest->send($apiClient);

		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20190425131559'), $paymentResponse->getResponseDateTime());
		self::assertEquals(ResultCode::get(ResultCode::C0_OK), $paymentResponse->getResultCode());
		self::assertSame('OK', $paymentResponse->getResultMessage());
		self::assertNotEmpty($paymentResponse->getCheckoutParams());
	}

}
