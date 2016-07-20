<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Cart;
use SlevomatCsobGateway\Currency;
use SlevomatCsobGateway\Language;

class InitPaymentRequestTest extends \PHPUnit_Framework_TestCase
{

	public function testSend()
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
				'description' => 'Nákup na vasobchod.cz (Lenovo ThinkPad Edge E540, Doprava PPL)',
				'merchantData' => base64_encode('some-base64-encoded-merchant-data'),
				'customerId' => '123',
				'language' => 'CZ',
				'ttlSec' => 1800,
				'logoVersion' => 1,
				'colorSchemeVersion' => 2,
			])
			->willReturn(
				new Response(new ResponseCode(ResponseCode::S200_OK), [
					'payId' => '123456789',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 1,
				])
			);

		/** @var ApiClient $apiClient */
		$cart = new Cart(
			new Currency(Currency::CZK)
		);
		$cart->addItem('Nákup na vasobchodcz', 1, 1789600, 'Lenovo ThinkPad Edge E540');
		$cart->addItem('Poštovné', 1, 0, 'Doprava PPL');

		$initPaymentRequest = new InitPaymentRequest(
			'012345',
			'5547',
			new PayOperation(PayOperation::PAYMENT),
			new PayMethod(PayMethod::CARD),
			true,
			'https://vasobchod.cz/gateway-return',
			new HttpMethod(HttpMethod::POST),
			$cart,
			'Nákup na vasobchod.cz (Lenovo ThinkPad Edge E540, Doprava PPL)',
			'some-base64-encoded-merchant-data',
			'123',
			new Language(Language::CZ),
			1800,
			1,
			2
		);

		$paymentResponse = $initPaymentRequest->send($apiClient);

		$this->assertInstanceOf(PaymentResponse::class, $paymentResponse);
		$this->assertSame('123456789', $paymentResponse->getPayId());
		$this->assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $paymentResponse->getResponseDateTime());
		$this->assertEquals(new ResultCode(ResultCode::C0_OK), $paymentResponse->getResultCode());
		$this->assertSame('OK', $paymentResponse->getResultMessage());
		$this->assertEquals(new PaymentStatus(PaymentStatus::S1_CREATED), $paymentResponse->getPaymentStatus());
		$this->assertNull($paymentResponse->getAuthCode());
	}

}
