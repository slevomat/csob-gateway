<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\GooglePay;

use SlevomatCsobGateway\AdditionalData\Customer;
use SlevomatCsobGateway\AdditionalData\Order;
use SlevomatCsobGateway\Api\ApiClient;
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

class InitGooglePayRequest
{

	/**
	 * @param mixed[] $payload Complete payload from Google Pay JS API, containing paymentMethodData.tokenizationData.token
	 */
	public function __construct(
		private string $merchantId,
		private string $orderId,
		private string $clientIp,
		private Price $totalPrice,
		private ?bool $closePayment,
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

	public function send(ApiClient $apiClient): ActionsPaymentResponse
	{
		$payloadData = $this->payload['paymentMethodData']['tokenizationData']['token'] ?? null;
		if ($payloadData === null) {
			throw new InvalidJsonPayloadException('Missing `paymentMethodData.tokenizationData.token` in Google Pay payload.');
		}
		$payloadData = base64_encode((string) $payloadData);

		$requestData = array_filter([
			'merchantId' => $this->merchantId,
			'orderNo' => $this->orderId,
			'totalAmount' => $this->totalPrice->getAmount(),
			'currency' => $this->totalPrice->getCurrency()->value,
			'closePayment' => $this->closePayment,
			'clientIp' => $this->clientIp,
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
			'googlepay/init',
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
