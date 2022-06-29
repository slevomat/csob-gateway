<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\ApplePay;

use SlevomatCsobGateway\AdditionalData\Fingerprint;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Call\ActionsPaymentResponse;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;

class ProcessApplePayRequest
{

	public function __construct(
		private string $merchantId,
		private string $payId,
		private Fingerprint $fingerprint,
	)
	{
		Validator::checkPayId($payId);
	}

	public function send(ApiClient $apiClient): ActionsPaymentResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'payId' => $this->payId,
			'fingerprint' => $this->fingerprint->encode(),
		];

		$response = $apiClient->post(
			'applepay/process',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'payId' => null,
				'dttm' => null,
				'fingerprint' => Fingerprint::encodeForSignature(),
			]),
			new SignatureDataFormatter(ActionsPaymentResponse::encodeForSignature()),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return ActionsPaymentResponse::createFromResponseData($data);
	}

}
