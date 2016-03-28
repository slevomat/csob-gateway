<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Currency;
use SlevomatCsobGateway\Validator;

class RecurrentPaymentRequest
{

	/**
	 * @var string
	 */
	private $merchantId;

	/**
	 * @var string
	 */
	private $origPayId;

	/**
	 * @var string
	 */
	private $orderId;

	/**
	 * @var int|null
	 */
	private $totalAmount;

	/**
	 * @var Currency|null
	 */
	private $currency;

	/**
	 * @var string|null
	 */
	private $description;

	public function __construct(
		string $merchantId,
		string $origPayId,
		string $orderId,
		int $totalAmount = null,
		Currency $currency = null,
		string $description = null
	)
	{
		Validator::checkOrderId($orderId);
		if ($description !== null) {
			Validator::checkDescription($description);
		}

		$this->merchantId = $merchantId;
		$this->origPayId = $origPayId;
		$this->orderId = $orderId;
		$this->totalAmount = $totalAmount;
		$this->currency = $currency;
		$this->description = $description;
	}

	public function send(ApiClient $apiClient): PaymentResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'origPayId' => $this->origPayId,
			'orderNo' => $this->orderId,
		];

		if ($this->totalAmount !== null) {
			$requestData['totalAmount'] = $this->totalAmount;
		}

		if ($this->currency !== null) {
			$requestData['currency'] = $this->currency->getValue();
		}

		if ($this->description !== null) {
			$requestData['description'] = $this->description;
		}

		$response = $apiClient->post(
			'payment/recurrent',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'origPayId' => null,
				'orderNo' => null,
				'dttm' => null,
				'totalAmount' => null,
				'currency' => null,
				'description' => null,
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
			new ResultCode($data['resultCode']),
			$data['resultMessage'],
			array_key_exists('paymentStatus', $data) ? new PaymentStatus($data['paymentStatus']) : null,
			$data['authCode'] ?? null
		);
	}

}
