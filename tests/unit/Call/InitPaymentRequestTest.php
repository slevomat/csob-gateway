<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\AdditionalData\Customer;
use SlevomatCsobGateway\AdditionalData\CustomerAccount;
use SlevomatCsobGateway\AdditionalData\CustomerLogin;
use SlevomatCsobGateway\AdditionalData\CustomerLoginAuth;
use SlevomatCsobGateway\AdditionalData\Order;
use SlevomatCsobGateway\AdditionalData\OrderAddress;
use SlevomatCsobGateway\AdditionalData\OrderAvailability;
use SlevomatCsobGateway\AdditionalData\OrderDelivery;
use SlevomatCsobGateway\AdditionalData\OrderDeliveryMode;
use SlevomatCsobGateway\AdditionalData\OrderType;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Cart;
use SlevomatCsobGateway\Country;
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
				'customer' => [
					'name' => 'Jan Novák',
					'email' => 'jan.novak@example.com',
					'mobilePhone' => '+420.800300300',
					'account' => [
						'createdAt' => '2022-01-12T12:10:37+01:00',
						'changedAt' => '2022-01-15T15:10:12+01:00',
					],
					'login' => [
						'auth' => 'account',
						'authAt' => '2022-01-25T13:10:03+01:00',
					],
				],
				'order' => [
					'type' => 'purchase',
					'availability' => 'now',
					'delivery' => 'shipping',
					'deliveryMode' => '1',
					'addressMatch' => true,
					'billing' => [
						'address1' => 'Karlova 1',
						'city' => 'Praha',
						'zip' => '11000',
						'country' => 'CZE',
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
				new Response(ResponseCode::S200_OK, [
					'payId' => '123456789',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 1,
				]),
			);

		$cart = new Cart(
			Currency::CZK,
		);
		$cart->addItem('Nákup na vasobchodcz', 1, 1789600, 'Lenovo ThinkPad Edge E540');
		$cart->addItem('Poštovné', 1, 0, 'Doprava PPL');

		$customer = new Customer(
			'Jan Novák',
			'jan.novak@example.com',
			mobilePhone: '+420.800300300',
			customerAccount: new CustomerAccount(
				new DateTimeImmutable('2022-01-12T12:10:37+01:00'),
				new DateTimeImmutable('2022-01-15T15:10:12+01:00'),
			),
			customerLogin: new CustomerLogin(
				CustomerLoginAuth::ACCOUNT,
				new DateTimeImmutable('2022-01-25T13:10:03+01:00'),
			),
		);

		$order = new Order(
			OrderType::PURCHASE,
			OrderAvailability::NOW,
			null,
			OrderDelivery::SHIPPING,
			OrderDeliveryMode::SAME_DAY,
			addressMatch: true,
			billing: new OrderAddress(
				'Karlova 1',
				null,
				null,
				'Praha',
				'11000',
				null,
				Country::CZE,
			),
		);

		$initPaymentRequest = new InitPaymentRequest(
			'012345',
			'5547',
			PayOperation::PAYMENT,
			PayMethod::CARD,
			true,
			'https://vasobchod.cz/gateway-return',
			HttpMethod::POST,
			$cart,
			$customer,
			$order,
			'some-base64-encoded-merchant-data',
			'123',
			Language::CZ,
			1800,
			1,
			2,
		);

		$response = $initPaymentRequest->send($apiClient);

		self::assertSame('123456789', $response->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $response->getResponseDateTime());
		self::assertSame(ResultCode::C0_OK, $response->getResultCode());
		self::assertSame('OK', $response->getResultMessage());
		self::assertSame(PaymentStatus::S1_CREATED, $response->getPaymentStatus());
		self::assertNull($response->getCustomerCode());
	}

}
