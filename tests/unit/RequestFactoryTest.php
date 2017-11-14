<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Call\ClosePaymentRequest;
use SlevomatCsobGateway\Call\CustomerInfoRequest;
use SlevomatCsobGateway\Call\EchoRequest;
use SlevomatCsobGateway\Call\InitPaymentRequest;
use SlevomatCsobGateway\Call\Masterpass\BasicCheckoutRequest;
use SlevomatCsobGateway\Call\Masterpass\BasicFinishRequest;
use SlevomatCsobGateway\Call\Masterpass\StandardCheckoutRequest;
use SlevomatCsobGateway\Call\Masterpass\StandardExtractRequest;
use SlevomatCsobGateway\Call\Masterpass\StandardFinishRequest;
use SlevomatCsobGateway\Call\OneclickInitPaymentRequest;
use SlevomatCsobGateway\Call\OneclickStartPaymentRequest;
use SlevomatCsobGateway\Call\PaymentButtonBrand;
use SlevomatCsobGateway\Call\PaymentButtonRequest;
use SlevomatCsobGateway\Call\PaymentStatusRequest;
use SlevomatCsobGateway\Call\PayMethod;
use SlevomatCsobGateway\Call\PayOperation;
use SlevomatCsobGateway\Call\PostEchoRequest;
use SlevomatCsobGateway\Call\ProcessPaymentRequest;
use SlevomatCsobGateway\Call\ReceivePaymentRequest;
use SlevomatCsobGateway\Call\RefundPaymentRequest;
use SlevomatCsobGateway\Call\ReversePaymentRequest;

class RequestFactoryTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @var RequestFactory
	 */
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

		$request = $this->requestFactory->createInitPayment(
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

		$this->assertInstanceOf(InitPaymentRequest::class, $request);
	}

	public function testCreateProcessPayment(): void
	{
		$request = $this->requestFactory->createProcessPayment('123456789');

		$this->assertInstanceOf(ProcessPaymentRequest::class, $request);
	}

	public function testCreatePaymentStatus(): void
	{
		$request = $this->requestFactory->createPaymentStatus('123456789');

		$this->assertInstanceOf(PaymentStatusRequest::class, $request);
	}

	public function testCreateReversePayment(): void
	{
		$request = $this->requestFactory->createReversePayment('123456789');

		$this->assertInstanceOf(ReversePaymentRequest::class, $request);
	}

	public function testCreateClosePayment(): void
	{
		$request = $this->requestFactory->createClosePayment('123456789');

		$this->assertInstanceOf(ClosePaymentRequest::class, $request);
	}

	public function testCreateRefundPayment(): void
	{
		$request = $this->requestFactory->createRefundPayment('123456789');

		$this->assertInstanceOf(RefundPaymentRequest::class, $request);
	}

	public function testCreateEchoRequest(): void
	{
		$request = $this->requestFactory->createEchoRequest();

		$this->assertInstanceOf(EchoRequest::class, $request);
	}

	public function testCreatePostEchoRequest(): void
	{
		$request = $this->requestFactory->createPostEchoRequest();

		$this->assertInstanceOf(PostEchoRequest::class, $request);
	}

	public function testCreateCustomerInfo(): void
	{
		$request = $this->requestFactory->createCustomerInfo('cust123@mail.com');

		$this->assertInstanceOf(CustomerInfoRequest::class, $request);
	}

	public function testCreateReceivePayment(): void
	{
		$request = $this->requestFactory->createReceivePaymentRequest();

		$this->assertInstanceOf(ReceivePaymentRequest::class, $request);
	}

	public function testCreateOneclickInitPayment(): void
	{
		$request = $this->requestFactory->createOneclickInitPayment(
			'ef08b6e9f22345c',
			'5547123',
			new Price(1789600, Currency::get(Currency::CZK)),
			'Nákup na vasobchod.cz (Lenovo ThinkPad Edge E540, Doprava PPL)'
		);

		$this->assertInstanceOf(OneclickInitPaymentRequest::class, $request);
	}

	public function testCreateOneclickStartPayment(): void
	{
		$request = $this->requestFactory->createOneclickStartPayment('ef08b6e9f22345c');

		$this->assertInstanceOf(OneclickStartPaymentRequest::class, $request);
	}

	public function testCreateMasterpassBasicCheckoutRequest(): void
	{
		$request = $this->requestFactory->createMasterpassBasicCheckoutRequest('ef08b6e9f22345c', 'https://www.example.com/callback');

		$this->assertInstanceOf(BasicCheckoutRequest::class, $request);
	}

	public function testCreateMasterpassBasicFinishRequest(): void
	{
		$callbackParams = [
			'mpstatus' => 'success',
			'oauthToken' => '6a79bf9e320a0460d08aee7ad154f7dab4e19503',
			'checkoutResourceUrl' => 'https://sandbox.api.mastercard.com/masterpass/v6/checkout/616764812',
			'oauthVerifier' => 'fc8f41bb76ed7d43ea6d714cb8fdefa606611a7d',
		];
		$request = $this->requestFactory->createMasterpassBasicFinishRequest('ef08b6e9f22345c', $callbackParams);

		$this->assertInstanceOf(BasicFinishRequest::class, $request);
	}

	public function testCreateMasterpassStandardCheckoutRequest(): void
	{
		$request = $this->requestFactory->createMasterpassStandardCheckoutRequest('ef08b6e9f22345c', 'https://www.example.com/callback', 'SP123');

		$this->assertInstanceOf(StandardCheckoutRequest::class, $request);
	}

	public function testCreateMasterpassStandardExtractRequest(): void
	{
		$callbackParams = [
			'mpstatus' => 'success',
			'oauthToken' => '6a79bf9e320a0460d08aee7ad154f7dab4e19503',
			'checkoutResourceUrl' => 'https://sandbox.api.mastercard.com/masterpass/v6/checkout/616764812',
			'oauthVerifier' => 'fc8f41bb76ed7d43ea6d714cb8fdefa606611a7d',
		];
		$request = $this->requestFactory->createMasterpassStandardExtractRequest('ef08b6e9f22345c', $callbackParams);

		$this->assertInstanceOf(StandardExtractRequest::class, $request);
	}

	public function testCreateMasterpassStandardFinishRequest(): void
	{
		$request = $this->requestFactory->createMasterpassStandardFinishRequest('ef08b6e9f22345c', '123456789', 15000);

		$this->assertInstanceOf(StandardFinishRequest::class, $request);
	}

	public function testCreatePaymentButtonRequest(): void
	{
		$request = $this->requestFactory->createPaymentButtonRequest('ef08b6e9f22345c', PaymentButtonBrand::get(PaymentButtonBrand::ERA));

		$this->assertInstanceOf(PaymentButtonRequest::class, $request);
	}

}
