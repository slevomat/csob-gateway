<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\ApplePay;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;

class StartApplePayRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('applepay/start', [
				'merchantId' => '012345',
				'payId' => 'ef08b6e9f22345c',
				'payload' => 'eyJmb28iOiJiYXIifQ==',
			])
			->willReturn(
				new Response(ResponseCode::get(ResponseCode::S200_OK), [
					'payId' => 'ef08b6e9f22345c',
					'dttm' => '20190425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 2,
				])
			);

		$initPaymentRequest = new StartApplePayRequest(
			'012345',
			'ef08b6e9f22345c',
			['foo' => 'bar'],
			null
		);

		$paymentResponse = $initPaymentRequest->send($apiClient);

		self::assertSame('ef08b6e9f22345c', $paymentResponse->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20190425131559'), $paymentResponse->getResponseDateTime());
		self::assertEquals(ResultCode::get(ResultCode::C0_OK), $paymentResponse->getResultCode());
		self::assertSame('OK', $paymentResponse->getResultMessage());
		self::assertEquals(PaymentStatus::get(PaymentStatus::S2_IN_PROGRESS), $paymentResponse->getPaymentStatus());
		self::assertNull($paymentResponse->getAuthCode());
	}

}
