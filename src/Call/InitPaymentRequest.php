<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use InvalidArgumentException;
use SlevomatCsobGateway\AdditionalData\Customer;
use SlevomatCsobGateway\AdditionalData\Order;
use SlevomatCsobGateway\Api\ApiClientInterface;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Cart;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\Language;
use SlevomatCsobGateway\Validator;
use function array_filter;
use function base64_encode;
use function sprintf;

class InitPaymentRequest
{

	public function __construct(
		private string $merchantId,
		private string $orderId,
		private PayOperation $payOperation,
		private PayMethod $payMethod,
		private bool $closePayment,
		private string $returnUrl,
		private HttpMethod $returnMethod,
		private Cart $cart,
		private ?Customer $customer,
		private ?Order $order,
		private ?string $merchantData,
		private ?string $customerId,
		private Language $language,
		private ?int $ttlSec = null,
		private ?int $logoVersion = null,
		private ?int $colorSchemeVersion = null,
		private ?DateTimeImmutable $customExpiry = null,
	)
	{
		Validator::checkOrderId($this->orderId);
		Validator::checkReturnUrl($this->returnUrl);
		Validator::checkReturnMethod($this->returnMethod);
		if ($this->merchantData !== null) {
			Validator::checkMerchantData($this->merchantData);
		}
		if ($this->customerId !== null) {
			Validator::checkCustomerId($this->customerId);
		}
		if ($this->ttlSec !== null) {
			Validator::checkTtlSec($this->ttlSec);
		}

		if ($this->payOperation === PayOperation::CUSTOM_PAYMENT && $this->customExpiry === null) {
			throw new InvalidArgumentException(sprintf('Custom expiry parameter is required for custom payment.'));
		}
	}

	public function send(ApiClientInterface $apiClient): InitPaymentResponse
	{
		$price = $this->cart->getCurrentPrice();

		$requestData = array_filter([
			'merchantId' => $this->merchantId,
			'orderNo' => $this->orderId,
			'payOperation' => $this->payOperation->value,
			'payMethod' => $this->payMethod->value,
			'totalAmount' => $price->getAmount(),
			'currency' => $price->getCurrency()->value,
			'closePayment' => $this->closePayment,
			'returnUrl' => $this->returnUrl,
			'returnMethod' => $this->returnMethod->value,
			'cart' => $this->cart->encode(),
			'customer' => $this->customer?->encode(),
			'order' => $this->order?->encode(),
			'merchantData' => $this->merchantData !== null ? base64_encode($this->merchantData) : null,
			'customerId' => $this->customerId,
			'language' => $this->language->value,
			'ttlSec' => $this->ttlSec,
			'logoVersion' => $this->logoVersion,
			'colorSchemeVersion' => $this->colorSchemeVersion,
			'customExpiry' => $this->customExpiry?->format('YmdHis'),
		], EncodeHelper::filterValueCallback());

		$response = $apiClient->post(
			'payment/init',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'orderNo' => null,
				'dttm' => null,
				'payOperation' => null,
				'payMethod' => null,
				'totalAmount' => null,
				'currency' => null,
				'closePayment' => null,
				'returnUrl' => null,
				'returnMethod' => null,
				'cart' => Cart::encodeForSignature(),
				'customer' => Customer::encodeForSignature(),
				'order' => Order::encodeForSignature(),
				'merchantData' => null,
				'customerId' => null,
				'language' => null,
				'ttlSec' => null,
				'logoVersion' => null,
				'colorSchemeVersion' => null,
				'customExpiry' => null,
			]),
			new SignatureDataFormatter(InitPaymentResponse::encodeForSignature()),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return InitPaymentResponse::createFromResponseData($data);
	}

}
