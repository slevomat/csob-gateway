<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use function array_key_exists;
use function base64_decode;
use function is_numeric;

class ReceivePaymentRequest
{

	/**
	 * @param ApiClient $apiClient
	 * @param mixed[] $data
	 * @return PaymentResponse
	 */
	public function send(ApiClient $apiClient, array $data): PaymentResponse
	{
		if (array_key_exists('resultCode', $data) && is_numeric($data['resultCode'])) {
			$data['resultCode'] = (int) $data['resultCode'];
		}

		if (array_key_exists('paymentStatus', $data) && is_numeric($data['paymentStatus'])) {
			$data['paymentStatus'] = (int) $data['paymentStatus'];
		}

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
			ResultCode::get($data['resultCode']),
			$data['resultMessage'],
			isset($data['paymentStatus']) ? PaymentStatus::get($data['paymentStatus']) : null,
			$data['authCode'] ?? null,
			isset($data['merchantData']) ? (string) base64_decode($data['merchantData'], true) : null
		);
	}

}
