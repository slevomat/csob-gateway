<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class RefundPaymentRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('put')
			->with('payment/refund', [
				'merchantId' => '012345',
				'payId' => '123456789',
			])
			->willReturn(
				new Response(ResponseCode::S200_OK, [
					'payId' => '123456789',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 8,
				]),
			);

		$refundPaymentRequest = new RefundPaymentRequest(
			'012345',
			'123456789',
		);

		$response = $refundPaymentRequest->send($apiClient);

		self::assertSame('123456789', $response->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $response->getResponseDateTime());
		self::assertSame(ResultCode::C0_OK, $response->getResultCode());
		self::assertSame('OK', $response->getResultMessage());
		self::assertSame(PaymentStatus::S8_CHARGED, $response->getPaymentStatus());
		self::assertNull($response->getAuthCode());
		self::assertNull($response->getStatusDetail());
	}

}
