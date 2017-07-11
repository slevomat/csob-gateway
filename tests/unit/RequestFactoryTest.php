<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Call\ClosePaymentRequest;
use SlevomatCsobGateway\Call\CustomerInfoRequest;
use SlevomatCsobGateway\Call\EchoRequest;
use SlevomatCsobGateway\Call\InitPaymentRequest;
use SlevomatCsobGateway\Call\OneclickInitPaymentRequest;
use SlevomatCsobGateway\Call\OneclickStartPaymentRequest;
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

	protected function setUp()
	{
		$this->requestFactory = new RequestFactory('012345');
	}

	public function testCreateInitPayment()
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

	public function testCreateProcessPayment()
	{
		$request = $this->requestFactory->createProcessPayment('123456789');

		$this->assertInstanceOf(ProcessPaymentRequest::class, $request);
	}

	public function testCreatePaymentStatus()
	{
		$request = $this->requestFactory->createPaymentStatus('123456789');

		$this->assertInstanceOf(PaymentStatusRequest::class, $request);
	}

	public function testCreateReversePayment()
	{
		$request = $this->requestFactory->createReversePayment('123456789');

		$this->assertInstanceOf(ReversePaymentRequest::class, $request);
	}

	public function testCreateClosePayment()
	{
		$request = $this->requestFactory->createClosePayment('123456789');

		$this->assertInstanceOf(ClosePaymentRequest::class, $request);
	}

	public function testCreateRefundPayment()
	{
		$request = $this->requestFactory->createRefundPayment('123456789');

		$this->assertInstanceOf(RefundPaymentRequest::class, $request);
	}

	public function testCreateEchoRequest()
	{
		$request = $this->requestFactory->createEchoRequest();

		$this->assertInstanceOf(EchoRequest::class, $request);
	}

	public function testCreatePostEchoRequest()
	{
		$request = $this->requestFactory->createPostEchoRequest();

		$this->assertInstanceOf(PostEchoRequest::class, $request);
	}

	public function testCreateCustomerInfo()
	{
		$request = $this->requestFactory->createCustomerInfo('cust123@mail.com');

		$this->assertInstanceOf(CustomerInfoRequest::class, $request);
	}

	public function testCreateReceivePayment()
	{
		$request = $this->requestFactory->createReceivePaymentRequest();

		$this->assertInstanceOf(ReceivePaymentRequest::class, $request);
	}

	public function testCreateOneclickInitPayment()
	{
		$request = $this->requestFactory->createOneclickInitPayment(
			'ef08b6e9f22345c',
			'5547123',
			new Price(1789600, Currency::get(Currency::CZK)),
			'Nákup na vasobchod.cz (Lenovo ThinkPad Edge E540, Doprava PPL)'
		);

		$this->assertInstanceOf(OneclickInitPaymentRequest::class, $request);
	}

	public function testCreateOneclickStartPayment()
	{
		$request = $this->requestFactory->createOneclickStartPayment('ef08b6e9f22345c');

		$this->assertInstanceOf(OneclickStartPaymentRequest::class, $request);
	}

}
