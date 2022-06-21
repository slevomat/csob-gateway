<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;

class ClosePaymentRequest
{

	public function __construct(
		private string $merchantId,
		private string $payId,
		private ?int $totalAmount = null,
	)
	{
		Validator::checkPayId($payId);
	}

	public function send(ApiClient $apiClient): PaymentResponse
	{
		$data = [
			'merchantId' => $this->merchantId,
			'payId' => $this->payId,
		];

		if ($this->totalAmount !== null) {
			$data['totalAmount'] = $this->totalAmount;
		}

		$response = $apiClient->put(
			'payment/close',
			$data,
			new SignatureDataFormatter([
				'merchantId' => null,
				'payId' => null,
				'dttm' => null,
				'totalAmount' => null,
			]),
			new SignatureDataFormatter([
				'payId' => null,
				'dttm' => null,
				'resultCode' => null,
				'resultMessage' => null,
				'paymentStatus' => null,
				'authCode' => null,
			]),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return new PaymentResponse(
			$data['payId'],
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			ResultCode::from($data['resultCode']),
			$data['resultMessage'],
			isset($data['paymentStatus']) ? PaymentStatus::from($data['paymentStatus']) : null,
			$data['authCode'] ?? null,
		);
	}

}
