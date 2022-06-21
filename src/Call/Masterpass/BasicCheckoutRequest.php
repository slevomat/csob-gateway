<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Masterpass;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;

class BasicCheckoutRequest
{

	public function __construct(
		private string $merchantId,
		private string $payId,
		private string $callbackUrl,
	)
	{
		Validator::checkPayId($payId);
		Validator::checkReturnUrl($callbackUrl);
	}

	public function send(ApiClient $apiClient): CheckoutResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'payId' => $this->payId,
			'callbackUrl' => $this->callbackUrl,
		];

		$response = $apiClient->post(
			'masterpass/basic/checkout',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'payId' => null,
				'dttm' => null,
				'callbackUrl' => null,
			]),
			new SignatureDataFormatter([
				'payId' => null,
				'dttm' => null,
				'resultCode' => null,
				'resultMessage' => null,
				'paymentStatus' => null,
				'lightboxParams' => [
					'requestToken' => null,
					'callbackUrl' => null,
					'merchantCheckoutId' => null,
					'allowedCardTypes' => null,
					'suppressShippingAddressEnable' => null,
					'loyaltyEnabled' => null,
					'version' => null,
					'shippingLocationProfile' => null,
				],
			]),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return new CheckoutResponse(
			$data['payId'],
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			ResultCode::get($data['resultCode']),
			$data['resultMessage'],
			isset($data['paymentStatus']) ? PaymentStatus::get($data['paymentStatus']) : null,
			$data['lightboxParams'] ?? null,
		);
	}

}
