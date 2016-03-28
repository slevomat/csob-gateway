<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Call\ClosePaymentRequest;
use SlevomatCsobGateway\Call\CustomerInfoRequest;
use SlevomatCsobGateway\Call\EchoRequest;
use SlevomatCsobGateway\Call\InitPaymentRequest;
use SlevomatCsobGateway\Call\PaymentStatusRequest;
use SlevomatCsobGateway\Call\PayMethod;
use SlevomatCsobGateway\Call\PayOperation;
use SlevomatCsobGateway\Call\PostEchoRequest;
use SlevomatCsobGateway\Call\ProcessPaymentRequest;
use SlevomatCsobGateway\Call\ReceivePaymentRequest;
use SlevomatCsobGateway\Call\RecurrentPaymentRequest;
use SlevomatCsobGateway\Call\RefundPaymentRequest;
use SlevomatCsobGateway\Call\ReversePaymentRequest;

class RequestFactory
{

	/**
	 * @var string
	 */
	private $merchantId;

	public function __construct(string $merchantId)
	{
		$this->merchantId = $merchantId;
	}

	public function createInitPayment(
		string $orderId,
		PayOperation $payOperation,
		PayMethod $payMethod,
		bool $closePayment,
		string $returnUrl,
		HttpMethod $returnMethod,
		Cart $cart,
		string $description,
		string $merchantData = null,
		string $customerId = null,
		Language $language = null
	): InitPaymentRequest
	{
		return new InitPaymentRequest(
			$this->merchantId,
			$orderId,
			$payOperation,
			$payMethod,
			$closePayment,
			$returnUrl,
			$returnMethod,
			$cart,
			$description,
			$merchantData,
			$customerId,
			$language
		);
	}

	public function createProcessPayment(string $payId): ProcessPaymentRequest
	{
		return new ProcessPaymentRequest(
			$this->merchantId,
			$payId
		);
	}

	public function createPaymentStatus(string $payId): PaymentStatusRequest
	{
		return new PaymentStatusRequest(
			$this->merchantId,
			$payId
		);
	}

	public function createReversePayment(string $payId): ReversePaymentRequest
	{
		return new ReversePaymentRequest(
			$this->merchantId,
			$payId
		);
	}

	public function createClosePayment(string $payId): ClosePaymentRequest
	{
		return new ClosePaymentRequest(
			$this->merchantId,
			$payId
		);
	}

	public function createRefundPayment(string $payId, int $amount = null): RefundPaymentRequest
	{
		return new RefundPaymentRequest(
			$this->merchantId,
			$payId,
			$amount
		);
	}

	public function createRecurrentPayment(
		string $origPayId,
		string $orderId,
		int $totalAmount = null,
		Currency $currency = null,
		string $description = null
	): RecurrentPaymentRequest
	{
		return new RecurrentPaymentRequest(
			$this->merchantId,
			$origPayId,
			$orderId,
			$totalAmount,
			$currency,
			$description
		);
	}

	public function createEchoRequest(): EchoRequest
	{
		return new EchoRequest(
			$this->merchantId
		);
	}

	public function createPostEchoRequest(): PostEchoRequest
	{
		return new PostEchoRequest(
			$this->merchantId
		);
	}

	public function createCustomerInfo(string $customerId): CustomerInfoRequest
	{
		return new CustomerInfoRequest(
			$this->merchantId,
			$customerId
		);
	}

	public function createReceivePaymentRequest(): ReceivePaymentRequest
	{
		return new ReceivePaymentRequest();
	}

}
