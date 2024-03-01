<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\OneClick;

use SlevomatCsobGateway\AdditionalData\Customer;
use SlevomatCsobGateway\AdditionalData\Order;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Call\ActionsPaymentResponse;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\Language;
use SlevomatCsobGateway\Price;
use SlevomatCsobGateway\Validator;
use function array_filter;
use function base64_encode;

class InitOneClickPaymentRequest
{

	public function __construct(
		private string $merchantId,
		private string $origPayId,
		private string $orderId,
		private ?string $clientIp,
		private ?Price $price,
		private ?bool $closePayment,
		private string $returnUrl,
		private HttpMethod $returnMethod,
		private ?Customer $customer = null,
		private ?Order $order = null,
		private ?bool $clientInitiated = null,
		private ?bool $sdkUsed = null,
		private ?string $merchantData = null,
		private ?Language $language = null,
		private ?int $ttlSec = null,
	)
	{
		Validator::checkPayId($this->origPayId);
		Validator::checkOrderId($this->orderId);
		Validator::checkReturnUrl($this->returnUrl);
		Validator::checkReturnMethod($this->returnMethod);
		if ($this->merchantData !== null) {
			Validator::checkMerchantData($this->merchantData);
		}
	}

	public function send(ApiClient $apiClient): ActionsPaymentResponse
	{
		$requestData = array_filter([
			'merchantId' => $this->merchantId,
			'origPayId' => $this->origPayId,
			'orderNo' => $this->orderId,
			'clientIp' => $this->clientIp,
			'totalAmount' => $this->price?->getAmount(),
			'currency' => $this->price?->getCurrency()->value,
			'closePayment' => $this->closePayment,
			'returnUrl' => $this->returnUrl,
			'returnMethod' => $this->returnMethod->value,
			'customer' => $this->customer?->encode(),
			'order' => $this->order?->encode(),
			'clientInitiated' => $this->clientInitiated,
			'sdkUsed' => $this->sdkUsed,
			'merchantData' => $this->merchantData !== null ? base64_encode($this->merchantData) : null,
			'language' => $this->language?->value,
			'ttlSec' => $this->ttlSec,
		], EncodeHelper::filterValueCallback());

		$response = $apiClient->post(
			'oneclick/init',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'origPayId' => null,
				'orderNo' => null,
				'dttm' => null,
				'clientIp' => null,
				'totalAmount' => null,
				'currency' => null,
				'closePayment' => null,
				'returnUrl' => null,
				'returnMethod' => null,
				'customer' => Customer::encodeForSignature(),
				'order' => Order::encodeForSignature(),
				'clientInitiated' => null,
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
