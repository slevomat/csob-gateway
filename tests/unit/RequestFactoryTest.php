<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\AdditionalData\Fingerprint;
use SlevomatCsobGateway\AdditionalData\FingerprintBrowser;
use SlevomatCsobGateway\AdditionalData\FingerprintSdk;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Call\Button\PaymentButtonBrand;
use SlevomatCsobGateway\Call\PayMethod;
use SlevomatCsobGateway\Call\PayOperation;
use SlevomatCsobGateway\MallPay\AddressType;
use SlevomatCsobGateway\MallPay\CancelReason;
use SlevomatCsobGateway\MallPay\Customer;
use SlevomatCsobGateway\MallPay\LogisticsEvent;
use SlevomatCsobGateway\MallPay\Order;
use SlevomatCsobGateway\MallPay\OrderCarrierId;
use SlevomatCsobGateway\MallPay\OrderDeliveryType;
use SlevomatCsobGateway\MallPay\OrderItemReference;
use SlevomatCsobGateway\MallPay\OrderItemType;
use SlevomatCsobGateway\MallPay\OrderReference;
use SlevomatCsobGateway\MallPay\Vat;

class RequestFactoryTest extends TestCase
{

	private RequestFactory $requestFactory;

	protected function setUp(): void
	{
		$this->requestFactory = new RequestFactory('012345');
	}

	public function testCreateInitPayment(): void
	{
		$cart = new Cart(
			Currency::CZK,
		);
		$cart->addItem('Nákup na vasobchodcz', 1, 1789600, 'Lenovo ThinkPad Edge E540');
		$cart->addItem('Poštovné', 1, 0, 'Doprava PPL');

		$customer = new \SlevomatCsobGateway\AdditionalData\Customer('Pepa Zdepa', 'pepa@zdepa.cz', '420.123 456 789', '00420.12 34 56 789', '+420.123456789');

		$this->requestFactory->createInitPayment(
			'5547',
			PayOperation::PAYMENT,
			PayMethod::CARD,
			true,
			'https://vasobchod.cz/gateway-return',
			HttpMethod::POST,
			$cart,
			$customer,
			null,
			'some-base64-encoded-merchant-data',
			'123',
			Language::CZ,
			1800,
			1,
			1,
		);

		$this->expectNotToPerformAssertions();
	}

	public function testCreateProcessPayment(): void
	{
		$this->requestFactory->createProcessPayment('123456789');

		$this->expectNotToPerformAssertions();
	}

	public function testCreatePaymentStatus(): void
	{
		$this->requestFactory->createPaymentStatus('123456789');

		$this->expectNotToPerformAssertions();
	}

	public function testCreateReversePayment(): void
	{
		$this->requestFactory->createReversePayment('123456789');

		$this->expectNotToPerformAssertions();
	}

	public function testCreateClosePayment(): void
	{
		$this->requestFactory->createClosePayment('123456789');

		$this->expectNotToPerformAssertions();
	}

	public function testCreateRefundPayment(): void
	{
		$this->requestFactory->createRefundPayment('123456789');

		$this->expectNotToPerformAssertions();
	}

	public function testCreateEchoRequest(): void
	{
		$this->requestFactory->createEchoRequest();

		$this->expectNotToPerformAssertions();
	}

	public function testCreatePostEchoRequest(): void
	{
		$this->requestFactory->createPostEchoRequest();

		$this->expectNotToPerformAssertions();
	}

	public function testCreateEchoCustomer(): void
	{
		$this->requestFactory->createEchoCustomer('cust123@mail.com');

		$this->expectNotToPerformAssertions();
	}

	public function testCreateReceivePayment(): void
	{
		$this->requestFactory->createReceivePaymentRequest();

		$this->expectNotToPerformAssertions();
	}

	public function testCreateOneclickInitPayment(): void
	{
		$this->requestFactory->createOneclickInitPayment(
			'ef08b6e9f22345c',
			'5547123',
			'127.0.0.1',
			new Price(1789600, Currency::CZK),
			false,
			'https://shop.example.com/return',
			HttpMethod::POST,
		);

		$this->expectNotToPerformAssertions();
	}

	public function testCreateOneclickProcessPayment(): void
	{
		$this->requestFactory->createOneclickProcessPayment('ef08b6e9f22345c');

		$this->expectNotToPerformAssertions();
	}

	public function testCreatePaymentButtonRequest(): void
	{
		$this->requestFactory->createPaymentButtonRequest(
			'123456',
			'::1',
			new Price(12500, Currency::CZK),
			'https://www.example.com/return',
			HttpMethod::GET,
			PaymentButtonBrand::CSOB,
			null,
			Language::EN,
		);

		$this->expectNotToPerformAssertions();
	}

	public function testCreateApplePayEchoRequest(): void
	{
		$this->requestFactory->createApplePayEchoRequest();

		$this->expectNotToPerformAssertions();
	}

	public function testCreateApplePayInitRequest(): void
	{
		$this->requestFactory->createApplePayInitRequest(
			'1234567',
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
		);

		$this->expectNotToPerformAssertions();
	}

	public function testCreateApplePayProcessRequest(): void
	{
		$this->requestFactory->createApplePayProcessRequest(
			'ef08b6e9f22345c',
			new Fingerprint(
				new FingerprintBrowser(
					'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.99 Safari/537.36',
					'text/html,application/xhtml+xml,application/xml;',
					'en',
					false,
					null,
					null,
					null,
					null,
					null,
					null,
				),
			),
		);

		$this->expectNotToPerformAssertions();
	}

	public function testCreateOneClickEchoRequest(): void
	{
		$this->requestFactory->createOneClickEchoRequest('ef08b6e9f22345c');

		$this->expectNotToPerformAssertions();
	}

	public function testCreateMallPayCancelRequest(): void
	{
		$this->requestFactory->createMallPayCancelRequest('ef08b6e9f22345c', CancelReason::ABANDONED);

		$this->expectNotToPerformAssertions();
	}

	public function testCreateMallPayInitRequest(): void
	{
		$customer = new Customer(
			'John',
			'Doe',
			null,
			null,
			null,
			'john@example.com',
			'+420601123456',
			null,
			null,
		);
		$order = new Order(
			Currency::CZK,
			OrderDeliveryType::DELIVERY_CARRIER,
			OrderCarrierId::CZ_POST_OFFICE,
			'123456',
		);
		$order->addItem(
			'123',
			null,
			'Thing',
			OrderItemType::PHYSICAL,
			2,
			null,
			null,
			null,
			null,
			50000,
			100000,
			12000,
			24000,
			21,
			null,
		);
		$order->addAddress(
			'John Doe',
			Country::CZE,
			'Praha 8',
			'Pernerova',
			'42',
			'186 00',
			AddressType::BILLING,
		);

		$this->requestFactory->createMallPayInitRequest(
			'1234567',
			$customer,
			$order,
			false,
			'127.0.0.1',
			HttpMethod::GET,
			'https://www.example.com/return',
			null,
			null,
		);

		$this->expectNotToPerformAssertions();
	}

	public function testCreateMMallPayLogisticsRequest(): void
	{
		$orderReference = new OrderReference(new Price(100000, Currency::CZK), [new Vat(100000, Currency::CZK, 21)]);
		$orderReference->addItem('123', null, 'Thing', null, 1);
		$this->requestFactory->createMallPayLogisticsRequest(
			'ef08b6e9f22345c',
			LogisticsEvent::SENT,
			new DateTimeImmutable('2021-01-01'),
			$orderReference,
			null,
			'123456',
		);

		$this->expectNotToPerformAssertions();
	}

	public function testCreateMallPayRefundRequest(): void
	{
		$orderItemReference = new OrderItemReference('123', null, 'Thing', OrderItemType::DIGITAL, 1);
		$this->requestFactory->createMallPayRefundRequest('ef08b6e9f22345c', null, [$orderItemReference]);

		$this->expectNotToPerformAssertions();
	}

	public function testCreateGooglePayInitRequest(): void
	{
		$this->requestFactory->createGooglePayInitRequest(
			'1234567',
			'127.0.0.1',
			new Price(1789600, Currency::CZK),
			true,
			[],
			'https://www.example.com/return',
			HttpMethod::POST,
		);

		$this->expectNotToPerformAssertions();
	}

	public function testCreateGooglePayProcessRequest(): void
	{
		$this->requestFactory->createGooglePayProcessRequest(
			'ef08b6e9f22345c',
			new Fingerprint(
				null,
				new FingerprintSdk(
					'198d0791-0025-4183-b9ae-900c88dd80e0',
					'encrypted-data',
					'encoded-public-key',
					5,
					'sdk-reference-number',
					'7f101033-df46-4f5c-9e96-9575c924e1e7',
				),
			),
		);

		$this->expectNotToPerformAssertions();
	}

	public function testCreateGooglePayEchoRequest(): void
	{
		$this->requestFactory->createGooglePayEchoRequest();

		$this->expectNotToPerformAssertions();
	}

}
