<?php

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;

class ReceivePaymentRequest
{

	/**
	 * @param ApiClient $apiClient
	 * @param mixed[] $data
	 * @return PaymentResponse
	 */
	public function send(ApiClient $apiClient, array $data)
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
			array_key_exists('paymentStatus', $data) ? new PaymentStatus($data['paymentStatus']) : null,
			array_key_exists('authCode', $data) ? $data['authCode'] : null,
			array_key_exists('merchantData', $data) ? base64_decode($data['merchantData']) : null
		);
	}

}
