<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Button;

use SlevomatCsobGateway\Api\ApiClientInterface;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\Language;
use SlevomatCsobGateway\Price;
use SlevomatCsobGateway\Validator;
use function array_filter;
use function base64_encode;

class PaymentButtonRequest
{

	public function __construct(
		private string $merchantId,
		private string $orderId,
		private string $clientIp,
		private Price $totalPrice,
		private string $returnUrl,
		private HttpMethod $returnMethod,
		private ?PaymentButtonBrand $brand,
		private ?string $merchantData,
		private Language $language,
	)
	{
		Validator::checkReturnUrl($this->returnUrl);
		Validator::checkReturnMethod($this->returnMethod);
		if ($this->merchantData !== null) {
			Validator::checkMerchantData($this->merchantData);
		}
	}

	public function send(ApiClientInterface $apiClient): PaymentButtonResponse
	{
		$requestData = array_filter([
			'merchantId' => $this->merchantId,
			'orderNo' => $this->orderId,
			'clientIp' => $this->clientIp,
			'totalAmount' => $this->totalPrice->getAmount(),
			'currency' => $this->totalPrice->getCurrency()->value,
			'returnUrl' => $this->returnUrl,
			'returnMethod' => $this->returnMethod->value,
			'brand' => $this->brand?->value,
			'merchantData' => $this->merchantData !== null ? base64_encode($this->merchantData) : null,
			'language' => $this->language->value,
		], EncodeHelper::filterValueCallback());

		$response = $apiClient->post(
			'button/init',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'orderNo' => null,
				'dttm' => null,
				'clientIp' => null,
				'totalAmount' => null,
				'currency' => null,
				'returnUrl' => null,
				'returnMethod' => null,
				'brand' => null,
				'merchantData' => null,
				'language' => null,
			]),
			new SignatureDataFormatter(PaymentButtonResponse::encodeForSignature()),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return PaymentButtonResponse::createFromResponseData($data);
	}

}
