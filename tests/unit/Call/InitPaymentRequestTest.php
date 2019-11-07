<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Cart;
use SlevomatCsobGateway\Currency;
use SlevomatCsobGateway\Language;
use function base64_encode;

class InitPaymentRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('payment/init', [
				'merchantId' => '012345',
				'orderNo' => '5547',
				'payOperation' => 'payment',
				'payMethod' => 'card',
				'totalAmount' => 1789600,
				'currency' => 'CZK',
				'closePayment' => true,
				'returnUrl' => 'https://vasobchod.cz/gateway-return',
				'returnMethod' => 'POST',
				'cart' => [
					[
						'name' => 'Nákup na vasobchodcz',
						'quantity' => 1,
						'amount' => 1789600,
						'description' => 'Lenovo ThinkPad Edge E540',
					],
					[
						'name' => 'Poštovné',
						'quantity' => 1,
						'amount' => 0,
						'description' => 'Doprava PPL',
					],
				],
				'merchantData' => base64_encode('some-base64-encoded-merchant-data'),
				'customerId' => '123',
				'language' => 'CZ',
				'ttlSec' => 1800,
				'logoVersion' => 1,
				'colorSchemeVersion' => 2,
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

		/** @var ApiClient $apiClient */
		$cart = new Cart(
			Currency::get(Currency::CZK)
		);
		$cart->addItem('Nákup na vasobchodcz', 1, 1789600, 'Lenovo ThinkPad Edge E540');
		$cart->addItem('Poštovné', 1, 0, 'Doprava PPL');

		$initPaymentRequest = new InitPaymentRequest(
			'012345',
			'5547',
			PayOperation::get(PayOperation::PAYMENT),
			PayMethod::get(PayMethod::CARD),
			true,
			'https://vasobchod.cz/gateway-return',
			HttpMethod::get(HttpMethod::POST),
			$cart,
			'some-base64-encoded-merchant-data',
			'123',
			Language::get(Language::CZ),
			1800,
			1,
			2
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
