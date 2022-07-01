<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class ReceivePaymentRequestTest extends TestCase
{

	public function testSend(): void
	{
		$postData = [
			'payId' => '123456789',
			'dttm' => '20140425131559',
			'resultCode' => 0,
			'resultMessage' => 'OK',
			'paymentStatus' => 5,
			'signature' => 'signature',
		];

		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('createResponseByData')
			->willReturnCallback(static fn (array $postData): Response => new Response(ResponseCode::S200_OK, $postData));

		$receivePaymentRequest = new ReceivePaymentRequest();

		$paymentResponse = $receivePaymentRequest->send($apiClient, $postData);

		self::assertSame('123456789', $paymentResponse->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $paymentResponse->getResponseDateTime());
		self::assertSame(ResultCode::C0_OK, $paymentResponse->getResultCode());
		self::assertSame('OK', $paymentResponse->getResultMessage());
		self::assertSame(PaymentStatus::S5_REVOKED, $paymentResponse->getPaymentStatus());
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
			'signature' => 'signature',
		];

		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('createResponseByData')
			->willReturnCallback(static fn (array $postData): Response => new Response(ResponseCode::S200_OK, $postData));

		$receivePaymentRequest = new ReceivePaymentRequest();

		$paymentResponse = $receivePaymentRequest->send($apiClient, $postData);

		self::assertSame('123456789', $paymentResponse->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $paymentResponse->getResponseDateTime());
		self::assertEquals(ResultCode::C0_OK, $paymentResponse->getResultCode());
		self::assertSame('OK', $paymentResponse->getResultMessage());
		self::assertEquals(PaymentStatus::S5_REVOKED, $paymentResponse->getPaymentStatus());
		self::assertNull($paymentResponse->getAuthCode());
	}

	public function testSendWithMissingValues(): void
	{
		$postData = [
			'dttm' => '20140425131559',
			'resultCode' => '0',
			'resultMessage' => 'OK',
			'paymentStatus' => '5',
			'signature' => 'signature',
		];

		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$receivePaymentRequest = new ReceivePaymentRequest();

		try {
			$receivePaymentRequest->send($apiClient, $postData);
			self::fail();

		} catch (InvalidArgumentException $e) {
			self::assertSame('Missing parameter payId in gateway response', $e->getMessage());
		}
	}

}
