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
		Language $language,
		int $ttlSec = null,
		int $logoVersion = null,
		int $colorSchemeVersion = null
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
			$language,
			$ttlSec,
			$logoVersion,
			$colorSchemeVersion
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

	public function createClosePayment(string $payId, int $totalAmount = null): ClosePaymentRequest
	{
		return new ClosePaymentRequest(
			$this->merchantId,
			$payId,
			$totalAmount
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

	public function createOneclickInitPayment(
		string $origPayId,
		string $orderId,
		Price $price = null,
		string $description = null
	): OneclickInitPaymentRequest
	{
		return new OneclickInitPaymentRequest(
			$this->merchantId,
			$origPayId,
			$orderId,
			$price,
			$description
		);
	}

	public function createOneclickStartPayment(string $payId): OneclickStartPaymentRequest
	{
		return new OneclickStartPaymentRequest(
			$this->merchantId,
			$payId
		);
	}

}
