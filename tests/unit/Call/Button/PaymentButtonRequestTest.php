<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Button;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Currency;
use SlevomatCsobGateway\Language;
use SlevomatCsobGateway\Price;

class PaymentButtonRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('button/init', [
				'merchantId' => '012345',
				'orderNo' => '123456789',
				'clientIp' => '127.0.0.1',
				'totalAmount' => 12000,
				'currency' => 'CZK',
				'returnUrl' => 'https://www.example.com/return',
				'returnMethod' => 'GET',
				'brand' => 'csob',
				'language' => 'EN',
			])
			->willReturn(
				new Response(ResponseCode::get(ResponseCode::S200_OK), [
					'payId' => '123456789',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 1,
					'redirect' => [
						'method' => 'GET',
						'url' => 'https://platebnibrana.csob.cz/pay/vasobchod.cz/2c72d818-9788-45a1-878a-9db2a706edc5/pt-detect/csob',
					],
				])
			);

		$paymentRequest = new PaymentButtonRequest(
			'012345',
			'123456789',
			'127.0.0.1',
			new Price(12000, Currency::get(Currency::CZK)),
			'https://www.example.com/return',
			HttpMethod::get(HttpMethod::GET),
			PaymentButtonBrand::get(PaymentButtonBrand::CSOB),
			null,
			Language::get(Language::EN)
		);

		$paymentButtonResponse = $paymentRequest->send($apiClient);

		self::assertSame('123456789', $paymentButtonResponse->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $paymentButtonResponse->getResponseDateTime());
		self::assertEquals(ResultCode::get(ResultCode::C0_OK), $paymentButtonResponse->getResultCode());
		self::assertSame('OK', $paymentButtonResponse->getResultMessage());
		self::assertEquals(PaymentStatus::get(PaymentStatus::S1_CREATED), $paymentButtonResponse->getPaymentStatus());
		self::assertSame('https://platebnibrana.csob.cz/pay/vasobchod.cz/2c72d818-9788-45a1-878a-9db2a706edc5/pt-detect/csob', $paymentButtonResponse->getRedirectUrl());
		self::assertSame(HttpMethod::get(HttpMethod::GET), $paymentButtonResponse->getRedirectMethod());
		self::assertNull($paymentButtonResponse->getRedirectParams());
	}

}
