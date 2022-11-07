<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\ApplePay;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\AdditionalData\Customer;
use SlevomatCsobGateway\AdditionalData\CustomerLogin;
use SlevomatCsobGateway\AdditionalData\CustomerLoginAuth;
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
				'payload' => 'eyJ2ZXJzaW9uIjoiRUNfdjEiLCJkYXRhIjoiekR3Y2xRMS4uLi4iLCJzaWduYXR1cmUiOiJNSUFHQ1NxR1NJLi4uIiwiaGVhZGVyIjp7ImVwaGVtZXJhbFB1YmxpY0tleSI6Ik1Ga3dFd1kuLi4iLCJwdWJsaWNLZXlIYXNoIjoiYkhBYVpLMmswU00uLi4iLCJ0cmFuc2FjdGlvbklkIjoiNTMyNGI0OTlmYWI3Li4uIn19',
				'returnUrl' => 'https://shop.example.com/return',
				'returnMethod' => 'POST',
				'customer' => [
					'name' => 'Jan Novák',
					'login' => [
						'auth' => 'federated',
						'authData' => 'some data',
					],
				],
				'order' => [
					'type' => 'purchase',
					'availability' => 'now',
					'delivery' => 'digital',
					'deliveryMode' => '0',
					'deliveryEmail' => 'delivery@example.com',
					'reorder' => false,
				],
			])
			->willReturn(
				new Response(ResponseCode::S200_OK, [
					'payId' => '123456789',
					'dttm' => '20190425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 1,
					'actions' => [
						'fingerprint' => [
							'sdkInit' => [
								'directoryServerID' => 'A000000003',
								'schemeId' => 'Visa',
								'messageVersion' => '2.2.0',
							],
						],
					],
				]),
			);

		$request = new InitApplePayRequest(
			'012345',
			'12345',
			'127.0.0.1',
			new Price(1789600, Currency::CZK),
			true,
			[
				'paymentData' => [
					'version' => 'EC_v1',
					'data' => 'zDwclQ1....',
					'signature' => 'MIAGCSqGSI...',
					'header' => [
						'ephemeralPublicKey' => 'MFkwEwY...',
						'publicKeyHash' => 'bHAaZK2k0SM...',
						'transactionId' => '5324b499fab7...',
					],
				],
				'paymentMethod' => [
					'displayName' => 'MasterCard 1234',
					'network' => 'MasterCard',
					'type' => 'debit',
				],
				'transactionIdentifier' => '5324B499F...',
			],
			'https://shop.example.com/return',
			HttpMethod::POST,
			new Customer(
				'Jan Novák',
				customerLogin: new CustomerLogin(
					CustomerLoginAuth::FEDERATED,
					null,
					'some data',
				),
			),
			new Order(
				OrderType::PURCHASE,
				OrderAvailability::NOW,
				null,
				OrderDelivery::DIGITAL,
				OrderDeliveryMode::ELECTRONIC,
				'delivery@example.com',
				reorder: false,
			),
		);

		$response = $request->send($apiClient);

		self::assertSame('123456789', $response->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20190425131559'), $response->getResponseDateTime());
		self::assertSame(ResultCode::C0_OK, $response->getResultCode());
		self::assertSame('OK', $response->getResultMessage());
		self::assertSame(PaymentStatus::S1_CREATED, $response->getPaymentStatus());
		self::assertNull($response->getStatusDetail());
		self::assertNull($response->getActions()?->getAuthenticate());
		self::assertSame('A000000003', $response->getActions()?->getFingerprint()?->getSdkInit()?->getDirectoryServerID());
		self::assertSame('Visa', $response->getActions()->getFingerprint()->getSdkInit()->getSchemeId());
		self::assertSame('2.2.0', $response->getActions()->getFingerprint()->getSdkInit()->getMessageVersion());
	}

}
