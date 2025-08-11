<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\ApplePay;

use SlevomatCsobGateway\AdditionalData\Customer;
use SlevomatCsobGateway\AdditionalData\Order;
use SlevomatCsobGateway\Api\ApiClientInterface;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Call\ActionsPaymentResponse;
use SlevomatCsobGateway\Call\InvalidJsonPayloadException;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\Language;
use SlevomatCsobGateway\Price;
use SlevomatCsobGateway\Validator;
use function array_filter;
use function base64_encode;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use const JSON_ERROR_NONE;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class InitApplePayRequest
{

	/**
	 * @param mixed[] $payload Complete payload from Apple Pay JS API, containing paymentData.
	 */
	public function __construct(
		private string $merchantId,
		private string $orderId,
		private string $clientIp,
		private Price $totalPrice,
		private bool $closePayment,
		private array $payload,
		private string $returnUrl,
		private HttpMethod $returnMethod,
		private ?Customer $customer = null,
		private ?Order $order = null,
		private ?bool $sdkUsed = null,
		private ?string $merchantData = null,
		private ?Language $language = null,
		private ?int $ttlSec = null,
	)
	{
		Validator::checkOrderId($this->orderId);
		Validator::checkReturnUrl($this->returnUrl);
		Validator::checkReturnMethod($this->returnMethod);
		if ($this->merchantData !== null) {
			Validator::checkMerchantData($this->merchantData);
		}
		if ($this->ttlSec !== null) {
			Validator::checkTtlSec($this->ttlSec);
		}
	}

	public function send(ApiClientInterface $apiClient): ActionsPaymentResponse
	{
		$payloadData = $this->payload['paymentData'] ?? null;
		if ($payloadData === null) {
			throw new InvalidJsonPayloadException('Missing `paymentData` in ApplePay payload.');
		}
		$payloadData = json_encode($payloadData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		$error = json_last_error();
		if ($error !== JSON_ERROR_NONE) {
			throw new InvalidJsonPayloadException(json_last_error_msg(), $error);
		}
		$payloadData = base64_encode((string) $payloadData);

		$requestData = array_filter([
			'merchantId' => $this->merchantId,
			'orderNo' => $this->orderId,
			'clientIp' => $this->clientIp,
			'totalAmount' => $this->totalPrice->getAmount(),
			'currency' => $this->totalPrice->getCurrency()->value,
			'closePayment' => $this->closePayment,
			'payload' => $payloadData,
			'returnUrl' => $this->returnUrl,
			'returnMethod' => $this->returnMethod->value,
			'customer' => $this->customer?->encode(),
			'order' => $this->order?->encode(),
			'sdkUsed' => $this->sdkUsed,
			'merchantData' => $this->merchantData !== null ? base64_encode($this->merchantData) : null,
			'language' => $this->language?->value,
			'ttlSec' => $this->ttlSec,
		], EncodeHelper::filterValueCallback());

		$response = $apiClient->post(
			'applepay/init',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'orderNo' => null,
				'dttm' => null,
				'clientIp' => null,
				'totalAmount' => null,
				'currency' => null,
				'closePayment' => null,
				'payload' => null,
				'returnUrl' => null,
				'returnMethod' => null,
				'customer' => Customer::encodeForSignature(),
				'order' => Order::encodeForSignature(),
				'sdkUsed' => null,
				'merchantData' => null,
				'language' => null,
				'ttlSec' => null,
			]),
			new SignatureDataFormatter(ActionsPaymentResponse::encodeForSignature()),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return ActionsPaymentResponse::createFromResponseData($data);
	}

}
