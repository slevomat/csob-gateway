<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class ReceivePaymentRequestTest extends \PHPUnit_Framework_TestCase
{

	public function testSend()
	{
		$postData = [
			'payId' => '123456789',
			'dttm' => '20140425131559',
			'resultCode' => 0,
			'resultMessage' => 'OK',
			'paymentStatus' => 5,
		];

		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('createResponseByData')
			->willReturnCallback(function (array $postData) {
				return new Response(ResponseCode::get(ResponseCode::S200_OK), $postData);
			});

		/** @var ApiClient $apiClient */
		$receivePaymentRequest = new ReceivePaymentRequest();

		$paymentResponse = $receivePaymentRequest->send($apiClient, $postData);

		$this->assertInstanceOf(PaymentResponse::class, $paymentResponse);
		$this->assertSame('123456789', $paymentResponse->getPayId());
		$this->assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $paymentResponse->getResponseDateTime());
		$this->assertEquals(ResultCode::get(ResultCode::C0_OK), $paymentResponse->getResultCode());
		$this->assertSame('OK', $paymentResponse->getResultMessage());
		$this->assertEquals(PaymentStatus::get(PaymentStatus::S5_REVOKED), $paymentResponse->getPaymentStatus());
		$this->assertNull($paymentResponse->getAuthCode());
	}

	public function testSendWithStringValues()
	{
		$postData = [
			'payId' => '123456789',
			'dttm' => '20140425131559',
			'resultCode' => '0',
			'resultMessage' => 'OK',
			'paymentStatus' => '5',
		];

		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('createResponseByData')
			->willReturnCallback(function (array $postData) {
				return new Response(ResponseCode::get(ResponseCode::S200_OK), $postData);
			});

		/** @var ApiClient $apiClient */
		$receivePaymentRequest = new ReceivePaymentRequest();

		$paymentResponse = $receivePaymentRequest->send($apiClient, $postData);

		$this->assertInstanceOf(PaymentResponse::class, $paymentResponse);
		$this->assertSame('123456789', $paymentResponse->getPayId());
		$this->assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $paymentResponse->getResponseDateTime());
		$this->assertEquals(ResultCode::get(ResultCode::C0_OK), $paymentResponse->getResultCode());
		$this->assertSame('OK', $paymentResponse->getResultMessage());
		$this->assertEquals(PaymentStatus::get(PaymentStatus::S5_REVOKED), $paymentResponse->getPaymentStatus());
		$this->assertNull($paymentResponse->getAuthCode());
	}

}
