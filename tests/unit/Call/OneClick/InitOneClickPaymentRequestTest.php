<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\OneClick;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\AdditionalData\Customer;
use SlevomatCsobGateway\AdditionalData\Order;
use SlevomatCsobGateway\AdditionalData\OrderAvailability;
use SlevomatCsobGateway\AdditionalData\OrderDelivery;
use SlevomatCsobGateway\AdditionalData\OrderDeliveryMode;
use SlevomatCsobGateway\AdditionalData\OrderType;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\HttpMethod;
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
				'returnUrl' => 'https://shop.example.com/return',
				'returnMethod' => 'POST',
					'customer' => [
					'name' => 'Jan Novák',
					'email' => 'email@example.com',
					'homePhone' => '+420.800300300',
				],
				'order' => [
					'type' => 'purchase',
					'availability' => 'now',
					'delivery' => 'digital',
					'deliveryMode' => '0',
					'deliveryEmail' => 'delivery@example.com',
				],
				'merchantData' => base64_encode('some-base64-encoded-merchant-data'),
			])
			->willReturn(
				new Response(ResponseCode::S200_OK, [
					'payId' => '123456789',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 1,
					'actions' => [
						'fingerprint' => [
							'browserInit' => [
								'url' => 'https://example.com/3ds-method-endpoint',
							],
						],
					],
				]),
			);

		$request = new InitOneClickPaymentRequest(
			'012345',
			'ef08b6e9f22345c',
			'5547',
			'127.0.0.1',
			new Price(1789600, Currency::CZK),
			null,
			'https://shop.example.com/return',
			HttpMethod::POST,
			new Customer(
				'Jan Novák',
				'email@example.com',
				'+420.800300300',
			),
			new Order(
				OrderType::PURCHASE,
				OrderAvailability::NOW,
				null,
				OrderDelivery::DIGITAL,
				OrderDeliveryMode::ELECTRONIC,
				'delivery@example.com',
			),
			merchantData: 'some-base64-encoded-merchant-data',
		);

		$paymentResponse = $request->send($apiClient);

		self::assertSame('123456789', $paymentResponse->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $paymentResponse->getResponseDateTime());
		self::assertSame(ResultCode::C0_OK, $paymentResponse->getResultCode());
		self::assertSame('OK', $paymentResponse->getResultMessage());
		self::assertSame(PaymentStatus::S1_CREATED, $paymentResponse->getPaymentStatus());
		self::assertNull($paymentResponse->getStatusDetail());
		self::assertSame('https://example.com/3ds-method-endpoint', $paymentResponse->getActions()?->getFingerprint()?->getBrowserInit()?->getUrl());
		self::assertNull($paymentResponse->getActions()?->getAuthenticate());
	}

}
