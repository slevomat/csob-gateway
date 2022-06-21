<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\OneClick;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;

class StartOneClickPaymentRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('oneclick/start', [
				'merchantId' => '012345',
				'payId' => 'ef08b6e9f22345c',
			])
			->willReturn(
				new Response(ResponseCode::S200_OK, [
					'payId' => '123456789',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 2,
				]),
			);

		$initPaymentRequest = new StartOneClickPaymentRequest(
			'012345',
			'ef08b6e9f22345c',
		);

		$paymentResponse = $initPaymentRequest->send($apiClient);

		self::assertSame('123456789', $paymentResponse->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $paymentResponse->getResponseDateTime());
		self::assertEquals(ResultCode::C0_OK, $paymentResponse->getResultCode());
		self::assertSame('OK', $paymentResponse->getResultMessage());
		self::assertEquals(PaymentStatus::S2_IN_PROGRESS, $paymentResponse->getPaymentStatus());
		self::assertNull($paymentResponse->getAuthCode());
	}

}
