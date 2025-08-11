<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\OneClick;

use SlevomatCsobGateway\AdditionalData\Fingerprint;
use SlevomatCsobGateway\Api\ApiClientInterface;
use SlevomatCsobGateway\Call\ActionsPaymentResponse;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\Validator;
use function array_filter;

class ProcessOneClickPaymentRequest
{

	public function __construct(
		private string $merchantId,
		private string $payId,
		private ?Fingerprint $fingerprint = null,
	)
	{
		Validator::checkPayId($payId);
	}

	public function send(ApiClientInterface $apiClient): ActionsPaymentResponse
	{
		$requestData = array_filter([
			'merchantId' => $this->merchantId,
			'payId' => $this->payId,
			'fingerprint' => $this->fingerprint?->encode(),
		], EncodeHelper::filterValueCallback());

		$response = $apiClient->post(
			'oneclick/process',
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
