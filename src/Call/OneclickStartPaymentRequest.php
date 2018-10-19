<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;

class OneclickStartPaymentRequest
{

	/** @var string */
	private $merchantId;

	/** @var string */
	private $payId;

	public function __construct(
		string $merchantId,
		string $payId
	)
	{
		Validator::checkPayId($payId);

		$this->merchantId = $merchantId;
		$this->payId = $payId;
	}

	public function send(ApiClient $apiClient): PaymentResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'payId' => $this->payId,
		];

		$response = $apiClient->post(
			'payment/oneclick/start',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'payId' => null,
				'dttm' => null,
			]),
			new SignatureDataFormatter([
				'payId' => null,
				'dttm' => null,
				'resultCode' => null,
				'resultMessage' => null,
				'paymentStatus' => null,
			])
		);

		$data = $response->getData();

		return new PaymentResponse(
			$data['payId'],
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			ResultCode::get($data['resultCode']),
			$data['resultMessage'],
			isset($data['paymentStatus']) ? PaymentStatus::get($data['paymentStatus']) : null
		);
	}

}
