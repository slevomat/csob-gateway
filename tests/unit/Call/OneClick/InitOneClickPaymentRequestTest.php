<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\OneClick;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Currency;
use SlevomatCsobGateway\Price;
use function base64_encode;

class InitOneClickPaymentRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('oneclick/init', [
				'merchantId' => '012345',
				'origPayId' => 'ef08b6e9f22345c',
				'orderNo' => '5547',
				'clientIp' => '127.0.0.1',
				'totalAmount' => 1789600,
				'currency' => 'CZK',
				'description' => 'Nákup na vasobchod.cz (Lenovo ThinkPad Edge E540, Doprava PPL)',
				'merchantData' => base64_encode('some-base64-encoded-merchant-data'),
			])
			->willReturn(
				new Response(ResponseCode::get(ResponseCode::S200_OK), [
					'payId' => '123456789',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 1,
				])
			);

		$initPaymentRequest = new InitOneClickPaymentRequest(
			'012345',
			'ef08b6e9f22345c',
			'5547',
			'127.0.0.1',
			new Price(1789600, Currency::get(Currency::CZK)),
			'Nákup na vasobchod.cz (Lenovo ThinkPad Edge E540, Doprava PPL)',
			'some-base64-encoded-merchant-data'
		);

		$paymentResponse = $initPaymentRequest->send($apiClient);

		self::assertSame('123456789', $paymentResponse->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $paymentResponse->getResponseDateTime());
		self::assertEquals(ResultCode::get(ResultCode::C0_OK), $paymentResponse->getResultCode());
		self::assertSame('OK', $paymentResponse->getResultMessage());
		self::assertEquals(PaymentStatus::get(PaymentStatus::S1_CREATED), $paymentResponse->getPaymentStatus());
		self::assertNull($paymentResponse->getAuthCode());
	}

}
