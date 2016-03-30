<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;

class ReceivePaymentRequest
{

	public function send(ApiClient $apiClient, array $data): PaymentResponse
	{
		$response = $apiClient->createResponseByData($data, new SignatureDataFormatter([
			'payId' => null,
			'dttm' => null,
			'resultCode' => null,
			'resultMessage' => null,
			'paymentStatus' => null,
			'authCode' => null,
			'merchantData' => null,
		]));

		$data = $response->getData();

		return new PaymentResponse(
			$data['payId'],
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			new ResultCode($data['resultCode']),
			$data['resultMessage'],
			isset($data['paymentStatus']) ? new PaymentStatus($data['paymentStatus']) : null,
			$data['authCode'] ?? null,
			isset($data['merchantData']) ? base64_decode($data['merchantData']) : null
		);
	}

}
