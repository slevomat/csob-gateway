<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;

class ClosePaymentRequest
{

	/** @var string */
	private $merchantId;

	/** @var string */
	private $payId;

	/** @var int|null */
	private $totalAmount;

	public function __construct(
		string $merchantId,
		string $payId,
		?int $totalAmount = null
	)
	{
		Validator::checkPayId($payId);

		$this->merchantId = $merchantId;
		$this->payId = $payId;
		$this->totalAmount = $totalAmount;
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
			])
		);

		$data = $response->getData();

		return new PaymentResponse(
			$data['payId'],
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			ResultCode::get($data['resultCode']),
			$data['resultMessage'],
			isset($data['paymentStatus']) ? PaymentStatus::get($data['paymentStatus']) : null,
			$data['authCode'] ?? null
		);
	}

}
