<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

use DateTimeImmutable;
use SlevomatCsobGateway\AdditionalData\Fingerprint;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Call\ApplePay\EchoApplePayRequest;
use SlevomatCsobGateway\Call\ApplePay\InitApplePayRequest;
use SlevomatCsobGateway\Call\ApplePay\ProcessApplePayRequest;
use SlevomatCsobGateway\Call\Button\PaymentButtonBrand;
use SlevomatCsobGateway\Call\Button\PaymentButtonRequest;
use SlevomatCsobGateway\Call\ClosePaymentRequest;
use SlevomatCsobGateway\Call\EchoCustomerRequest;
use SlevomatCsobGateway\Call\EchoRequest;
use SlevomatCsobGateway\Call\GooglePay\EchoGooglePayRequest;
use SlevomatCsobGateway\Call\GooglePay\InitGooglePayRequest;
use SlevomatCsobGateway\Call\GooglePay\StartGooglePayRequest;
use SlevomatCsobGateway\Call\InitPaymentRequest;
use SlevomatCsobGateway\Call\MallPay\CancelMallPayRequest;
use SlevomatCsobGateway\Call\MallPay\InitMallPayRequest;
use SlevomatCsobGateway\Call\MallPay\LogisticsMallPayRequest;
use SlevomatCsobGateway\Call\MallPay\RefundMallPayRequest;
use SlevomatCsobGateway\Call\Masterpass\BasicCheckoutRequest;
use SlevomatCsobGateway\Call\Masterpass\BasicFinishRequest;
use SlevomatCsobGateway\Call\Masterpass\StandardCheckoutRequest;
use SlevomatCsobGateway\Call\Masterpass\StandardExtractRequest;
use SlevomatCsobGateway\Call\Masterpass\StandardFinishRequest;
use SlevomatCsobGateway\Call\OneClick\EchoOneClickRequest;
use SlevomatCsobGateway\Call\OneClick\InitOneClickPaymentRequest;
use SlevomatCsobGateway\Call\OneClick\ProcessOneClickPaymentRequest;
use SlevomatCsobGateway\Call\PaymentStatusRequest;
use SlevomatCsobGateway\Call\PayMethod;
use SlevomatCsobGateway\Call\PayOperation;
use SlevomatCsobGateway\Call\PostEchoRequest;
use SlevomatCsobGateway\Call\ProcessPaymentRequest;
use SlevomatCsobGateway\Call\ReceivePaymentRequest;
use SlevomatCsobGateway\Call\RefundPaymentRequest;
use SlevomatCsobGateway\Call\ReversePaymentRequest;
use SlevomatCsobGateway\MallPay\CancelReason;
use SlevomatCsobGateway\MallPay\Customer;
use SlevomatCsobGateway\MallPay\LogisticsEvent;
use SlevomatCsobGateway\MallPay\Order;
use SlevomatCsobGateway\MallPay\OrderItemReference;
use SlevomatCsobGateway\MallPay\OrderReference;

class RequestFactory
{

	public function __construct(private string $merchantId)
	{
	}

	public function createInitPayment(
		string $orderId,
		PayOperation $payOperation,
		PayMethod $payMethod,
		bool $closePayment,
		string $returnUrl,
		HttpMethod $returnMethod,
		Cart $cart,
		?\SlevomatCsobGateway\AdditionalData\Customer $customer,
		?\SlevomatCsobGateway\AdditionalData\Order $order,
		?string $merchantData,
		?string $customerId,
		Language $language,
		?int $ttlSec = null,
		?int $logoVersion = null,
		?int $colorSchemeVersion = null,
		?DateTimeImmutable $customExpiry = null,
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
			$customer,
			$order,
			$merchantData,
			$customerId,
			$language,
			$ttlSec,
			$logoVersion,
			$colorSchemeVersion,
			$customExpiry,
		);
	}

	public function createProcessPayment(string $payId): ProcessPaymentRequest
	{
		return new ProcessPaymentRequest(
			$this->merchantId,
			$payId,
		);
	}

	public function createPaymentStatus(string $payId): PaymentStatusRequest
	{
		return new PaymentStatusRequest(
			$this->merchantId,
			$payId,
		);
	}

	public function createReversePayment(string $payId): ReversePaymentRequest
	{
		return new ReversePaymentRequest(
			$this->merchantId,
			$payId,
		);
	}

	public function createClosePayment(string $payId, ?int $totalAmount = null): ClosePaymentRequest
	{
		return new ClosePaymentRequest(
			$this->merchantId,
			$payId,
			$totalAmount,
		);
	}

	public function createRefundPayment(string $payId, ?int $amount = null): RefundPaymentRequest
	{
		return new RefundPaymentRequest(
			$this->merchantId,
			$payId,
			$amount,
		);
	}

	public function createEchoRequest(): EchoRequest
	{
		return new EchoRequest(
			$this->merchantId,
		);
	}

	public function createPostEchoRequest(): PostEchoRequest
	{
		return new PostEchoRequest(
			$this->merchantId,
		);
	}

	public function createEchoCustomer(string $customerId): EchoCustomerRequest
	{
		return new EchoCustomerRequest(
			$this->merchantId,
			$customerId,
		);
	}

	public function createReceivePaymentRequest(): ReceivePaymentRequest
	{
		return new ReceivePaymentRequest();
	}

	public function createOneclickInitPayment(
		string $origPayId,
		string $orderId,
		?string $clientIp,
		?Price $price,
		?bool $closePayment,
		string $returnUrl,
		HttpMethod $returnMethod,
		?\SlevomatCsobGateway\AdditionalData\Customer $customer = null,
		?\SlevomatCsobGateway\AdditionalData\Order $order = null,
		?bool $clientInitiated = null,
		?bool $sdkUsed = null,
		?string $merchantData = null,
	): InitOneClickPaymentRequest
	{
		return new InitOneClickPaymentRequest(
			$this->merchantId,
			$origPayId,
			$orderId,
			$clientIp,
			$price,
			$closePayment,
			$returnUrl,
			$returnMethod,
			$customer,
			$order,
			$clientInitiated,
			$sdkUsed,
			$merchantData,
		);
	}

	public function createOneclickProcessPayment(string $payId, ?Fingerprint $fingerprint = null): ProcessOneClickPaymentRequest
	{
		return new ProcessOneClickPaymentRequest(
			$this->merchantId,
			$payId,
			$fingerprint,
		);
	}

	public function createMasterpassBasicCheckoutRequest(string $payId, string $callbackUrl): BasicCheckoutRequest
	{
		return new BasicCheckoutRequest(
			$this->merchantId,
			$payId,
			$callbackUrl,
		);
	}

	/**
	 * @param mixed[] $callbackParams
	 */
	public function createMasterpassBasicFinishRequest(string $payId, array $callbackParams): BasicFinishRequest
	{
		return new BasicFinishRequest(
			$this->merchantId,
			$payId,
			$callbackParams,
		);
	}

	public function createMasterpassStandardCheckoutRequest(string $payId, string $callbackUrl, ?string $shippingLocationProfile = null): StandardCheckoutRequest
	{
		return new StandardCheckoutRequest($this->merchantId, $payId, $callbackUrl, $shippingLocationProfile);
	}

	/**
	 * @param mixed[] $callbackParams
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
			$totalAmount,
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
		Language $language,
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
			$language,
		);
	}

	public function createApplePayEchoRequest(): EchoApplePayRequest
	{
		return new EchoApplePayRequest($this->merchantId);
	}

	/**
	 * @param mixed[] $payload Complete payload from Apple Pay JS API, containing paymentData.
	 */
	public function createApplePayInitRequest(
		string $orderId,
		string $clientIp,
		Price $totalPrice,
		bool $closePayment,
		array $payload,
		string $returnUrl,
		HttpMethod $returnMethod,
		?\SlevomatCsobGateway\AdditionalData\Customer $customer = null,
		?\SlevomatCsobGateway\AdditionalData\Order $order = null,
		?bool $sdkUsed = null,
		?string $merchantData = null,
		?int $ttlSec = null,
	): InitApplePayRequest
	{
		return new InitApplePayRequest(
			$this->merchantId,
			$orderId,
			$clientIp,
			$totalPrice,
			$closePayment,
			$payload,
			$returnUrl,
			$returnMethod,
			$customer,
			$order,
			$sdkUsed,
			$merchantData,
			$ttlSec,
		);
	}

	public function createApplePayProcessRequest(string $payId, Fingerprint $fingerprint): ProcessApplePayRequest
	{
		return new ProcessApplePayRequest($this->merchantId, $payId, $fingerprint);
	}

	public function createOneClickEchoRequest(string $payId): EchoOneClickRequest
	{
		return new EchoOneClickRequest(
			$this->merchantId,
			$payId,
		);
	}

	public function createMallPayInitRequest(
		string $orderId,
		Customer $customer,
		Order $order,
		bool $agreeTC,
		string $clientIp,
		HttpMethod $returnMethod,
		string $returnUrl,
		?string $merchantData,
		?int $ttlSec,
	): InitMallPayRequest
	{
		return new InitMallPayRequest(
			$this->merchantId,
			$orderId,
			$customer,
			$order,
			$agreeTC,
			$clientIp,
			$returnMethod,
			$returnUrl,
			$merchantData,
			$ttlSec,
		);
	}

	public function createMallPayLogisticsRequest(
		string $payId,
		LogisticsEvent $event,
		DateTimeImmutable $date,
		OrderReference $fulfilled,
		?OrderReference $cancelled,
		?string $deliveryTrackingNumber,
	): LogisticsMallPayRequest
	{
		return new LogisticsMallPayRequest(
			$this->merchantId,
			$payId,
			$event,
			$date,
			$fulfilled,
			$cancelled,
			$deliveryTrackingNumber,
		);
	}

	public function createMallPayCancelRequest(
		string $payId,
		CancelReason $reason,
	): CancelMallPayRequest
	{
		return new CancelMallPayRequest(
			$this->merchantId,
			$payId,
			$reason,
		);
	}

	/**
	 * @param OrderItemReference[] $refundedItems
	 */
	public function createMallPayRefundRequest(
		string $payId,
		?int $amount,
		array $refundedItems,
	): RefundMallPayRequest
	{
		return new RefundMallPayRequest(
			$this->merchantId,
			$payId,
			$amount,
			$refundedItems,
		);
	}

	public function createGooglePayEchoRequest(): EchoGooglePayRequest
	{
		return new EchoGooglePayRequest($this->merchantId);
	}

	/**
	 * @param mixed[] $payload Complete payload from Google Pay JS API, containing paymentMethodData.tokenizationData.token
	 */
	public function createGooglePayInitRequest(
		string $orderId,
		string $clientIp,
		Price $totalPrice,
		?bool $closePayment,
		array $payload,
		string $returnUrl,
		HttpMethod $returnMethod,
		?\SlevomatCsobGateway\AdditionalData\Customer $customer = null,
		?\SlevomatCsobGateway\AdditionalData\Order $order = null,
		?bool $sdkUsed = null,
		?string $merchantData = null,
		?int $ttlSec = null,
	): InitGooglePayRequest
	{
		return new InitGooglePayRequest(
			$this->merchantId,
			$orderId,
			$clientIp,
			$totalPrice,
			$closePayment,
			$payload,
			$returnUrl,
			$returnMethod,
			$customer,
			$order,
			$sdkUsed,
			$merchantData,
			$ttlSec,
		);
	}

	/**
	 * @param mixed[] $payload Complete payload from Google Pay JS API, containing paymentMethodData.tokenizationData.token
	 */
	public function createGooglePayStartRequest(string $payId, array $payload): StartGooglePayRequest
	{
		return new StartGooglePayRequest(
			$this->merchantId,
			$payId,
			$payload,
		);
	}

}
