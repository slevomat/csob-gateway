<?php

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Currency;

class RecurrentPaymentRequestTest extends \PHPUnit_Framework_TestCase
{

	public function testSend()
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('payment/recurrent', [
				'merchantId' => '012345',
				'origPayId' => 'ef08b6e9f22345c',
				'totalAmount' => 99.8,
				'currency' => 'CZK',
				'orderNo' => '5547123',
				'description' => 'foo description',
			])
			->willReturn(
				new Response(new ResponseCode(ResponseCode::S200_OK), [
					'payId' => '123456789',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 7,
					'authCode' => '123456',
				])
			);

		/** @var ApiClient $apiClient */
		$recurrentPaymentRequest = new RecurrentPaymentRequest(
			'012345',
			'ef08b6e9f22345c',
			'5547123',
			99.8,
			new Currency(Currency::CZK),
			'foo description'
		);

		$paymentResponse = $recurrentPaymentRequest->send($apiClient);

		$this->assertInstanceOf(PaymentResponse::class, $paymentResponse);
		$this->assertSame('123456789', $paymentResponse->getPayId());
		$this->assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $paymentResponse->getResponseDateTime());
		$this->assertEquals(new ResultCode(ResultCode::C0_OK), $paymentResponse->getResultCode());
		$this->assertSame('OK', $paymentResponse->getResultMessage());
		$this->assertEquals(new PaymentStatus(PaymentStatus::S7_AWAITING_SETTLEMENT), $paymentResponse->getPaymentStatus());
		$this->assertSame('123456', $paymentResponse->getAuthCode());
	}

}
