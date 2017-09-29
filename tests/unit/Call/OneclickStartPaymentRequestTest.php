<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class OneclickStartPaymentRequestTest extends \PHPUnit\Framework\TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('payment/oneclick/start', [
				'merchantId' => '012345',
				'payId' => 'ef08b6e9f22345c',
			])
			->willReturn(
				new Response(ResponseCode::get(ResponseCode::S200_OK), [
					'payId' => '123456789',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 2,
				])
			);

		$initPaymentRequest = new OneclickStartPaymentRequest(
			'012345',
			'ef08b6e9f22345c'
		);

		$paymentResponse = $initPaymentRequest->send($apiClient);

		$this->assertInstanceOf(PaymentResponse::class, $paymentResponse);
		$this->assertSame('123456789', $paymentResponse->getPayId());
		$this->assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $paymentResponse->getResponseDateTime());
		$this->assertEquals(ResultCode::get(ResultCode::C0_OK), $paymentResponse->getResultCode());
		$this->assertSame('OK', $paymentResponse->getResultMessage());
		$this->assertEquals(PaymentStatus::get(PaymentStatus::S2_IN_PROGRESS), $paymentResponse->getPaymentStatus());
		$this->assertNull($paymentResponse->getAuthCode());
	}

}
