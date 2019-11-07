<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Call\PaymentButtonBrand;
use SlevomatCsobGateway\Call\PayMethod;
use SlevomatCsobGateway\Call\PayOperation;

class RequestFactoryTest extends TestCase
{

	/** @var RequestFactory */
	private $requestFactory;

	protected function setUp(): void
	{
		$this->requestFactory = new RequestFactory('012345');
	}

	public function testCreateInitPayment(): void
	{
		$cart = new Cart(
			Currency::get(Currency::CZK)
		);
		$cart->addItem('Nákup na vasobchodcz', 1, 1789600, 'Lenovo ThinkPad Edge E540');
		$cart->addItem('Poštovné', 1, 0, 'Doprava PPL');

		$this->requestFactory->createInitPayment(
			'5547',
			PayOperation::get(PayOperation::PAYMENT),
			PayMethod::get(PayMethod::CARD),
			true,
			'https://vasobchod.cz/gateway-return',
			HttpMethod::get(HttpMethod::POST),
			$cart,
			'Nákup na vasobchod.cz (Lenovo ThinkPad Edge E540, Doprava PPL)',
			'some-base64-encoded-merchant-data',
			'123',
			Language::get(Language::CZ),
			1800,
			1,
			1
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

	public function testCreateCustomerInfo(): void
	{
		$this->requestFactory->createCustomerInfo('cust123@mail.com');

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
			new Price(1789600, Currency::get(Currency::CZK)),
			'Nákup na vasobchod.cz (Lenovo ThinkPad Edge E540, Doprava PPL)'
		);

		$this->expectNotToPerformAssertions();
	}

	public function testCreateOneclickStartPayment(): void
	{
		$this->requestFactory->createOneclickStartPayment('ef08b6e9f22345c');

		$this->expectNotToPerformAssertions();
	}

	public function testCreateMasterpassBasicCheckoutRequest(): void
	{
		$this->requestFactory->createMasterpassBasicCheckoutRequest('ef08b6e9f22345c', 'https://www.example.com/callback');

		$this->expectNotToPerformAssertions();
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

		$this->expectNotToPerformAssertions();
	}

	public function testCreateMasterpassStandardCheckoutRequest(): void
	{
		$this->requestFactory->createMasterpassStandardCheckoutRequest('ef08b6e9f22345c', 'https://www.example.com/callback', 'SP123');

		$this->expectNotToPerformAssertions();
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

		$this->expectNotToPerformAssertions();
	}

	public function testCreateMasterpassStandardFinishRequest(): void
	{
		$this->requestFactory->createMasterpassStandardFinishRequest('ef08b6e9f22345c', '123456789', 15000);

		$this->expectNotToPerformAssertions();
	}

	public function testCreatePaymentButtonRequest(): void
	{
		$this->requestFactory->createPaymentButtonRequest('ef08b6e9f22345c', PaymentButtonBrand::get(PaymentButtonBrand::ERA));

		$this->expectNotToPerformAssertions();
	}

}
