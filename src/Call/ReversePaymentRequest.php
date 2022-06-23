<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;

class ReversePaymentRequest
{

	public function __construct(private string $merchantId, private string $payId)
	{
		Validator::checkPayId($payId);
	}

	public function send(ApiClient $apiClient): StatusDetailPaymentResponse
	{
		$response = $apiClient->put(
			'payment/reverse',
			[
				'merchantId' => $this->merchantId,
				'payId' => $this->payId,
			],
			new SignatureDataFormatter([
				'merchantId' => null,
				'payId' => null,
				'dttm' => null,
			]),
			new SignatureDataFormatter(StatusDetailPaymentResponse::encodeForSignature()),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return StatusDetailPaymentResponse::createFromResponseData($data);
	}

}
