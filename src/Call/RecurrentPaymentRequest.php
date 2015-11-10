<?php

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Currency;

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
	 * @var float|null
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

	/**
	 * @param string $merchantId
	 * @param string $origPayId
	 * @param string $orderId
	 * @param float|null $totalAmount
	 * @param Currency|null $currency
	 * @param string|null $description
	 */
	public function __construct(
		$merchantId,
		$origPayId,
		$orderId,
		$totalAmount = null,
		Currency $currency = null,
		$description = null
	)
	{
		$this->merchantId = $merchantId;
		$this->origPayId = $origPayId;
		$this->orderId = $orderId;
		$this->totalAmount = $totalAmount;
		$this->currency = $currency;
		$this->description = $description;
	}

	/**
	 * @param ApiClient $apiClient
	 * @return PaymentResponse
	 */
	public function send(ApiClient $apiClient)
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'origPayId' => $this->origPayId,
			'orderId' => $this->orderId,
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
			array_key_exists('authCode', $data) ? $data['authCode'] : null
		);
	}

}
