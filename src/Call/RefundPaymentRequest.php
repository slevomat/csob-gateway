<?php

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;

class RefundPaymentRequest
{

	/**
	 * @var string
	 */
	private $merchantId;

	/**
	 * @var string
	 */
	private $payId;

	/**
	 * @var integer|null
	 */
	private $amount;

	/**
	 * @param string $merchantId
	 * @param string $payId
	 * @param integer|null $amount
	 */
	public function __construct(
		$merchantId,
		$payId,
		$amount = null
	)
	{
		Validator::checkPayId($payId);

		$this->merchantId = $merchantId;
		$this->payId = $payId;
		$this->amount = $amount;
	}

	/**
	 * @param ApiClient $apiClient
	 * @return PaymentResponse
	 */
	public function send(ApiClient $apiClient)
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'payId' => $this->payId,
		];
		if ($this->amount !== null) {
			$requestData['amount'] = $this->amount;
		}
		$response = $apiClient->put(
			'payment/refund',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'payId' => null,
				'dttm' => null,
				'amount' => null,
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
