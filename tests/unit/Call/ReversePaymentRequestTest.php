<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class ReversePaymentRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('put')
			->with('payment/reverse', [
				'merchantId' => '012345',
				'payId' => '123456789',
			])
			->willReturn(
				new Response(ResponseCode::get(ResponseCode::S200_OK), [
					'payId' => '123456789',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 5,
				]),
			);

		$reversePaymentRequest = new ReversePaymentRequest(
			'012345',
			'123456789',
		);

		$paymentResponse = $reversePaymentRequest->send($apiClient);

		self::assertSame('123456789', $paymentResponse->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $paymentResponse->getResponseDateTime());
		self::assertEquals(ResultCode::get(ResultCode::C0_OK), $paymentResponse->getResultCode());
		self::assertSame('OK', $paymentResponse->getResultMessage());
		self::assertEquals(PaymentStatus::get(PaymentStatus::S5_REVOKED), $paymentResponse->getPaymentStatus());
		self::assertNull($paymentResponse->getAuthCode());
	}

}
