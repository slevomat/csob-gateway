<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class ClosePaymentRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('put')
			->with('payment/close', [
				'merchantId' => '012345',
				'payId' => '123456789',
				'totalAmount' => 987,
			])
			->willReturn(
				new Response(ResponseCode::get(ResponseCode::S200_OK), [
					'payId' => '123456789',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 7,
				])
			);

		/** @var ApiClient $apiClient */
		$paymentRequest = new ClosePaymentRequest(
			'012345',
			'123456789',
			987
		);

		$closePaymentResponse = $paymentRequest->send($apiClient);

		self::assertSame('123456789', $closePaymentResponse->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $closePaymentResponse->getResponseDateTime());
		self::assertEquals(ResultCode::get(ResultCode::C0_OK), $closePaymentResponse->getResultCode());
		self::assertSame('OK', $closePaymentResponse->getResultMessage());
		self::assertEquals(PaymentStatus::get(PaymentStatus::S7_AWAITING_SETTLEMENT), $closePaymentResponse->getPaymentStatus());
		self::assertNull($closePaymentResponse->getAuthCode());
	}

}
