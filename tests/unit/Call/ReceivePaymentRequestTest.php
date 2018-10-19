<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class ReceivePaymentRequestTest extends \PHPUnit\Framework\TestCase
{

	public function testSend(): void
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
			->willReturnCallback(static function (array $postData) {
				return new Response(ResponseCode::get(ResponseCode::S200_OK), $postData);
			});

		/** @var ApiClient $apiClient */
		$receivePaymentRequest = new ReceivePaymentRequest();

		$paymentResponse = $receivePaymentRequest->send($apiClient, $postData);

		self::assertInstanceOf(PaymentResponse::class, $paymentResponse);
		self::assertSame('123456789', $paymentResponse->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $paymentResponse->getResponseDateTime());
		self::assertEquals(ResultCode::get(ResultCode::C0_OK), $paymentResponse->getResultCode());
		self::assertSame('OK', $paymentResponse->getResultMessage());
		self::assertEquals(PaymentStatus::get(PaymentStatus::S5_REVOKED), $paymentResponse->getPaymentStatus());
		self::assertNull($paymentResponse->getAuthCode());
	}

	public function testSendWithStringValues(): void
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
			->willReturnCallback(static function (array $postData) {
				return new Response(ResponseCode::get(ResponseCode::S200_OK), $postData);
			});

		/** @var ApiClient $apiClient */
		$receivePaymentRequest = new ReceivePaymentRequest();

		$paymentResponse = $receivePaymentRequest->send($apiClient, $postData);

		self::assertInstanceOf(PaymentResponse::class, $paymentResponse);
		self::assertSame('123456789', $paymentResponse->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $paymentResponse->getResponseDateTime());
		self::assertEquals(ResultCode::get(ResultCode::C0_OK), $paymentResponse->getResultCode());
		self::assertSame('OK', $paymentResponse->getResultMessage());
		self::assertEquals(PaymentStatus::get(PaymentStatus::S5_REVOKED), $paymentResponse->getPaymentStatus());
		self::assertNull($paymentResponse->getAuthCode());
	}

}
