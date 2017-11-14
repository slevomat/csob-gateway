<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;

class PaymentButtonRequest
{

	/**
	 * @var string
	 */
	private $merchantId;

	/**
	 * @var string
	 */
	private $payId;

	/** @var \SlevomatCsobGateway\Call\PaymentButtonBrand */
	private $brand;

	public function __construct(
		string $merchantId,
		string $payId,
		PaymentButtonBrand $brand
	)
	{
		Validator::checkPayId($payId);

		$this->merchantId = $merchantId;
		$this->payId = $payId;
		$this->brand = $brand;
	}

	public function send(ApiClient $apiClient): PaymentButtonResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'payId' => $this->payId,
			'brand' => $this->brand->getValue(),
		];

		$response = $apiClient->post(
			'payment/button',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'payId' => null,
				'brand' => null,
				'dttm' => null,
			]),
			new SignatureDataFormatter([
				'payId' => null,
				'dttm' => null,
				'resultCode' => null,
				'resultMessage' => null,
				'paymentStatus' => null,
				'redirect' => [
					'method' => null,
					'url' => null,
					'params' => null,
				],
			])
		);

		$data = $response->getData();

		$redirectUrl = null;
		$redirectMethod = null;
		$redirectParams = [];
		if (isset($data['redirect'])) {
			$redirectUrl = $data['redirect']['url'];
			$redirectMethod = HttpMethod::get($data['redirect']['method']);
			$redirectParams = $data['redirect']['params'] ?? null;
		}

		return new PaymentButtonResponse(
			$data['payId'],
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			ResultCode::get($data['resultCode']),
			$data['resultMessage'],
			isset($data['paymentStatus']) ? PaymentStatus::get($data['paymentStatus']) : null,
			$redirectMethod,
			$redirectUrl,
			$redirectParams
		);
	}

}
