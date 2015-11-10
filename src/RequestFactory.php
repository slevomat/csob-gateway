<?php

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

	/**
	 * @param string $merchantId
	 */
	public function __construct(
		$merchantId
	)
	{
		$this->merchantId = (string) $merchantId;
	}

	/**
	 * @param string $orderId
	 * @param PayOperation $payOperation
	 * @param PayMethod $payMethod
	 * @param bool $closePayment
	 * @param string $returnUrl
	 * @param HttpMethod $returnMethod
	 * @param Cart $cart
	 * @param string $description
	 * @param string|null $merchantData
	 * @param string|null $customerId
	 * @param Language|null $language
	 * @return InitPaymentRequest
	 */
	public function createInitPayment(
		$orderId,
		PayOperation $payOperation,
		PayMethod $payMethod,
		$closePayment,
		$returnUrl,
		HttpMethod $returnMethod,
		Cart $cart,
		$description,
		$merchantData = null,
		$customerId = null,
		Language $language = null
	)
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

	/**
	 * @param string $payId
	 * @return ProcessPaymentRequest
	 */
	public function createProcessPayment($payId)
	{
		return new ProcessPaymentRequest(
			$this->merchantId,
			$payId
		);
	}

	/**
	 * @param string $payId
	 * @return PaymentStatusRequest
	 */
	public function createPaymentStatus($payId)
	{
		return new PaymentStatusRequest(
			$this->merchantId,
			$payId
		);
	}

	/**
	 * @param string $payId
	 * @return ReversePaymentRequest
	 */
	public function createReversePayment($payId)
	{
		return new ReversePaymentRequest(
			$this->merchantId,
			$payId
		);
	}

	/**
	 * @param string $payId
	 * @return ClosePaymentRequest
	 */
	public function createClosePayment($payId)
	{
		return new ClosePaymentRequest(
			$this->merchantId,
			$payId
		);
	}

	/**
	 * @param string $payId
	 * @return RefundPaymentRequest
	 */
	public function createRefundPayment($payId)
	{
		return new RefundPaymentRequest(
			$this->merchantId,
			$payId
		);
	}

	/**
	 * @param string $origPayId
	 * @param string $orderId
	 * @param float|null $totalAmount
	 * @param Currency|null $currency
	 * @param string|null $description
	 * @return RecurrentPaymentRequest
	 */
	public function createRecurrentPayment(
		$origPayId,
		$orderId,
		$totalAmount = null,
		Currency $currency = null,
		$description = null
	)
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

	/**
	 * @return EchoRequest
	 */
	public function createEchoRequest()
	{
		return new EchoRequest(
			$this->merchantId
		);
	}

	/**
	 * @return PostEchoRequest
	 */
	public function createPostEchoRequest()
	{
		return new PostEchoRequest(
			$this->merchantId
		);
	}

	/**
	 * @param string $customerId
	 * @return CustomerInfoRequest
	 */
	public function createCustomerInfo($customerId)
	{
		return new CustomerInfoRequest(
			$this->merchantId,
			$customerId
		);
	}

	/**
	 * @return ReceivePaymentRequest
	 */
	public function createReceivePaymentRequest()
	{
		return new ReceivePaymentRequest();
	}

}
