<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\ApplePay;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Currency;
use SlevomatCsobGateway\Price;

class InitApplePayRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('applepay/init', [
				'merchantId' => '012345',
				'orderNo' => '12345',
				'clientIp' => '127.0.0.1',
				'totalAmount' => 1789600,
				'currency' => 'CZK',
				'closePayment' => true,
			])
			->willReturn(
				new Response(ResponseCode::S200_OK, [
					'payId' => '123456789',
					'dttm' => '20190425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 1,
				]),
			);

		$initPaymentRequest = new InitApplePayRequest(
			'012345',
			'12345',
			'127.0.0.1',
			new Price(1789600, Currency::CZK),
			true,
			null,
		);

		$response = $initPaymentRequest->send($apiClient);

		self::assertSame('123456789', $response->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20190425131559'), $response->getResponseDateTime());
		self::assertEquals(ResultCode::C0_OK, $response->getResultCode());
		self::assertSame('OK', $response->getResultMessage());
		self::assertEquals(PaymentStatus::S1_CREATED, $response->getPaymentStatus());
		self::assertNull($response->getAuthCode());
	}

}
