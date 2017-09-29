<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class RefundPaymentRequestTest extends \PHPUnit\Framework\TestCase
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
				new Response(ResponseCode::get(ResponseCode::S200_OK), [
					'payId' => '123456789',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 8,
				])
			);

		/** @var ApiClient $apiClient */
		$refundPaymentRequest = new RefundPaymentRequest(
			'012345',
			'123456789'
		);

		$paymentResponse = $refundPaymentRequest->send($apiClient);

		$this->assertInstanceOf(PaymentResponse::class, $paymentResponse);
		$this->assertSame('123456789', $paymentResponse->getPayId());
		$this->assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $paymentResponse->getResponseDateTime());
		$this->assertEquals(ResultCode::get(ResultCode::C0_OK), $paymentResponse->getResultCode());
		$this->assertSame('OK', $paymentResponse->getResultMessage());
		$this->assertEquals(PaymentStatus::get(PaymentStatus::S8_CHARGED), $paymentResponse->getPaymentStatus());
		$this->assertNull($paymentResponse->getAuthCode());
	}

}
