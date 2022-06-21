<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Call\Button\PaymentButtonBrand;
use SlevomatCsobGateway\Call\PayMethod;
use SlevomatCsobGateway\Call\PayOperation;
use SlevomatCsobGateway\MallPay\AddressType;
use SlevomatCsobGateway\MallPay\CancelReason;
use SlevomatCsobGateway\MallPay\Country;
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

		$this->requestFactory->createInitPayment(
			'5547',
			PayOperation::PAYMENT,
			PayMethod::CARD,
			true,
			'https://vasobchod.cz/gateway-return',
			HttpMethod::POST,
			$cart,
			'some-base64-encoded-merchant-data',
			'123',
			Language::CZ,
			1800,
			1,
			1,
		);

		self::assertTrue(true);
	}

	public function testCreateProcessPayment(): void
	{
		$this->requestFactory->createProcessPayment('123456789');

		self::assertTrue(true);
	}

	public function testCreatePaymentStatus(): void
	{
		$this->requestFactory->createPaymentStatus('123456789');

		self::assertTrue(true);
	}

	public function testCreateReversePayment(): void
	{
		$this->requestFactory->createReversePayment('123456789');

		self::assertTrue(true);
	}

	public function testCreateClosePayment(): void
	{
		$this->requestFactory->createClosePayment('123456789');

		self::assertTrue(true);
	}

	public function testCreateRefundPayment(): void
	{
		$this->requestFactory->createRefundPayment('123456789');

		self::assertTrue(true);
	}

	public function testCreateEchoRequest(): void
	{
		$this->requestFactory->createEchoRequest();

		self::assertTrue(true);
	}

	public function testCreatePostEchoRequest(): void
	{
		$this->requestFactory->createPostEchoRequest();

		self::assertTrue(true);
	}

	public function testCreateCustomerInfo(): void
	{
		$this->requestFactory->createCustomerInfo('cust123@mail.com');

		self::assertTrue(true);
	}

	public function testCreateReceivePayment(): void
	{
		$this->requestFactory->createReceivePaymentRequest();

		self::assertTrue(true);
	}

	public function testCreateOneclickInitPayment(): void
	{
		$this->requestFactory->createOneclickInitPayment(
			'ef08b6e9f22345c',
			'5547123',
			'127.0.0.1',
			new Price(1789600, Currency::CZK),
			'Nákup na vasobchod.cz (Lenovo ThinkPad Edge E540, Doprava PPL)',
			'some-base64-encoded-merchant-data',
		);

		self::assertTrue(true);
	}

	public function testCreateOneclickStartPayment(): void
	{
		$this->requestFactory->createOneclickStartPayment('ef08b6e9f22345c');

		self::assertTrue(true);
	}

	public function testCreateMasterpassBasicCheckoutRequest(): void
	{
		$this->requestFactory->createMasterpassBasicCheckoutRequest('ef08b6e9f22345c', 'https://www.example.com/callback');

		self::assertTrue(true);
	}

	public function testCreateMasterpassBasicFinishRequest(): void
	{
		$callbackParams = [
			'mpstatus' => 'success',
			'oauthToken' => '6a79bf9e320a0460d08aee7ad154f7dab4e19503',
			'checkoutResourceUrl' => 'https://sandbox.api.mastercard.com/masterpass/v6/checkout/616764812',
			'oauthVerifier' => 'fc8f41bb76ed7d43ea6d714cb8fdefa606611a7d',
		];
		$this->requestFactory->createMasterpassBasicFinishRequest('ef08b6e9f22345c', $callbackParams);

		self::assertTrue(true);
	}

	public function testCreateMasterpassStandardCheckoutRequest(): void
	{
		$this->requestFactory->createMasterpassStandardCheckoutRequest('ef08b6e9f22345c', 'https://www.example.com/callback', 'SP123');

		self::assertTrue(true);
	}

	public function testCreateMasterpassStandardExtractRequest(): void
	{
		$callbackParams = [
			'mpstatus' => 'success',
			'oauthToken' => '6a79bf9e320a0460d08aee7ad154f7dab4e19503',
			'checkoutResourceUrl' => 'https://sandbox.api.mastercard.com/masterpass/v6/checkout/616764812',
			'oauthVerifier' => 'fc8f41bb76ed7d43ea6d714cb8fdefa606611a7d',
		];
		$this->requestFactory->createMasterpassStandardExtractRequest('ef08b6e9f22345c', $callbackParams);

		self::assertTrue(true);
	}

	public function testCreateMasterpassStandardFinishRequest(): void
	{
		$this->requestFactory->createMasterpassStandardFinishRequest('ef08b6e9f22345c', '123456789', 15000);

		self::assertTrue(true);
	}

	public function testCreatePaymentButtonRequest(): void
	{
		$this->requestFactory->createPaymentButtonRequest(
			'123456',
			'::1',
			new Price(12500, Currency::CZK),
			'https://www.example.com/return',
			HttpMethod::GET,
			PaymentButtonBrand::ERA,
			null,
			Language::EN,
		);

		self::assertTrue(true);
	}

	public function testCreateApplePayInitRequest(): void
	{
		$this->requestFactory->createApplePayInitRequest(
			'1234567',
			'127.0.0.1',
			new Price(1789600, Currency::CZK),
			true,
			'Order from example.com',
			null,
		);

		self::assertTrue(true);
	}

	public function testCreateApplePayStartRequest(): void
	{
		$this->requestFactory->createApplePayStartRequest(
			'ef08b6e9f22345c',
			[],
		);

		self::assertTrue(true);
	}

	public function testCreateOneClickEchoRequest(): void
	{
		$this->requestFactory->createOneClickEchoRequest('ef08b6e9f22345c');

		self::assertTrue(true);
	}

	public function testCreateMallPayCancelRequest(): void
	{
		$this->requestFactory->createMallPayCancelRequest('ef08b6e9f22345c', CancelReason::ABANDONED);

		self::assertTrue(true);
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

		self::assertTrue(true);
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

		self::assertTrue(true);
	}

	public function testCreateMallPayRefundRequest(): void
	{
		$orderItemReference = new OrderItemReference('123', null, 'Thing', OrderItemType::DIGITAL, 1);
		$this->requestFactory->createMallPayRefundRequest('ef08b6e9f22345c', null, [$orderItemReference]);

		self::assertTrue(true);
	}

	public function testCreateGooglePayInitRequest(): void
	{
		$this->requestFactory->createGooglePayInitRequest(
			'1234567',
			'127.0.0.1',
			new Price(1789600, Currency::CZK),
			true,
			'Order from example.com',
		);

		self::assertTrue(true);
	}

	public function testCreateGooglePayStartRequest(): void
	{
		$this->requestFactory->createGooglePayStartRequest(
			'ef08b6e9f22345c',
			[],
		);

		self::assertTrue(true);
	}

	public function testCreateGooglePayInfoRequest(): void
	{
		$this->requestFactory->createGooglePayInfoRequest();

		self::assertTrue(true);
	}

}
