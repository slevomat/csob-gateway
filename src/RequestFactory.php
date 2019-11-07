<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Call\ApplePay\InitApplePayRequest;
use SlevomatCsobGateway\Call\ApplePay\StartApplePayRequest;
use SlevomatCsobGateway\Call\Button\PaymentButtonBrand;
use SlevomatCsobGateway\Call\Button\PaymentButtonRequest;
use SlevomatCsobGateway\Call\ClosePaymentRequest;
use SlevomatCsobGateway\Call\CustomerInfoRequest;
use SlevomatCsobGateway\Call\EchoRequest;
use SlevomatCsobGateway\Call\InitPaymentRequest;
use SlevomatCsobGateway\Call\Masterpass\BasicCheckoutRequest;
use SlevomatCsobGateway\Call\Masterpass\BasicFinishRequest;
use SlevomatCsobGateway\Call\Masterpass\StandardCheckoutRequest;
use SlevomatCsobGateway\Call\Masterpass\StandardExtractRequest;
use SlevomatCsobGateway\Call\Masterpass\StandardFinishRequest;
use SlevomatCsobGateway\Call\OneClick\InitOneClickPaymentRequest;
use SlevomatCsobGateway\Call\OneClick\OneClickEchoRequest;
use SlevomatCsobGateway\Call\OneClick\StartOneClickPaymentRequest;
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

	/** @var string */
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
		?string $merchantData,
		?string $customerId,
		Language $language,
		?int $ttlSec = null,
		?int $logoVersion = null,
		?int $colorSchemeVersion = null,
		?DateTimeImmutable $customExpiry = null
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
			$merchantData,
			$customerId,
			$language,
			$ttlSec,
			$logoVersion,
			$colorSchemeVersion,
			$customExpiry
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

	public function createClosePayment(string $payId, ?int $totalAmount = null): ClosePaymentRequest
	{
		return new ClosePaymentRequest(
			$this->merchantId,
			$payId,
			$totalAmount
		);
	}

	public function createRefundPayment(string $payId, ?int $amount = null): RefundPaymentRequest
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
		string $clientIp,
		?Price $price = null,
		?string $description = null
	): InitOneClickPaymentRequest
	{
		return new InitOneClickPaymentRequest(
			$this->merchantId,
			$origPayId,
			$orderId,
			$clientIp,
			$price,
			$description
		);
	}

	public function createOneclickStartPayment(string $payId): StartOneClickPaymentRequest
	{
		return new StartOneClickPaymentRequest(
			$this->merchantId,
			$payId
		);
	}

	public function createMasterpassBasicCheckoutRequest(string $payId, string $callbackUrl): BasicCheckoutRequest
	{
		return new BasicCheckoutRequest(
			$this->merchantId,
			$payId,
			$callbackUrl
		);
	}

	/**
	 * @param string $payId
	 * @param mixed[] $callbackParams
	 * @return \SlevomatCsobGateway\Call\Masterpass\BasicFinishRequest
	 */
	public function createMasterpassBasicFinishRequest(string $payId, array $callbackParams): BasicFinishRequest
	{
		return new BasicFinishRequest(
			$this->merchantId,
			$payId,
			$callbackParams
		);
	}

	public function createMasterpassStandardCheckoutRequest(string $payId, string $callbackUrl, ?string $shippingLocationProfile = null): StandardCheckoutRequest
	{
		return new StandardCheckoutRequest($this->merchantId, $payId, $callbackUrl, $shippingLocationProfile);
	}

	/**
	 * @param string $payId
	 * @param mixed[] $callbackParams
	 * @return \SlevomatCsobGateway\Call\Masterpass\StandardExtractRequest
	 */
	public function createMasterpassStandardExtractRequest(string $payId, array $callbackParams): StandardExtractRequest
	{
		return new StandardExtractRequest($this->merchantId, $payId, $callbackParams);
	}

	public function createMasterpassStandardFinishRequest(string $payId, string $oauthToken, int $totalAmount): StandardFinishRequest
	{
		return new StandardFinishRequest(
			$this->merchantId,
			$payId,
			$oauthToken,
			$totalAmount
		);
	}

	public function createPaymentButtonRequest(
		string $orderId,
		string $clientIp,
		Price $totalPrice,
		string $returnUrl,
		HttpMethod $returnMethod,
		PaymentButtonBrand $brand,
		?string $merchantData,
		Language $language
	): PaymentButtonRequest
	{
		return new PaymentButtonRequest(
			$this->merchantId,
			$orderId,
			$clientIp,
			$totalPrice,
			$returnUrl,
			$returnMethod,
			$brand,
			$merchantData,
			$language
		);
	}

	public function createApplePayInitRequest(
		string $orderId,
		string $clientIp,
		Price $totalPrice,
		bool $closePayment,
		?string $merchantData,
		?int $ttlSec = null
	): InitApplePayRequest
	{
		return new InitApplePayRequest(
			$this->merchantId,
			$orderId,
			$clientIp,
			$totalPrice,
			$closePayment,
			$merchantData,
			$ttlSec
		);
	}

	/**
	 * @param string $payId
	 * @param mixed[] $payload
	 * @param int|null $totalAmount
	 * @return \SlevomatCsobGateway\Call\ApplePay\StartApplePayRequest
	 */
	public function createApplePayStartRequest(string $payId, array $payload, ?int $totalAmount = null): StartApplePayRequest
	{
		return new StartApplePayRequest(
			$this->merchantId,
			$payId,
			$payload,
			$totalAmount
		);
	}

	public function createOneClickEchoRequest(string $payId): OneClickEchoRequest
	{
		return new OneClickEchoRequest(
			$this->merchantId,
			$payId
		);
	}

}
