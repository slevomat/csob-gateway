<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\MallPay;

use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Call\StatusDetailPaymentResponse;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\MallPay\CancelReason;

class CancelMallPayRequest
{

	public function __construct(
		private string $merchantId,
		private string $payId,
		private CancelReason $reason,
	)
	{
	}

	public function send(ApiClient $apiClient): StatusDetailPaymentResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'payId' => $this->payId,
			'reason' => $this->reason->value,
		];

		$response = $apiClient->put(
			'mallpay/cancel',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'payId' => null,
				'reason' => null,
				'dttm' => null,
			]),
			new SignatureDataFormatter(StatusDetailPaymentResponse::encodeForSignature()),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return StatusDetailPaymentResponse::createFromResponseData($data);
	}

}
