<?php

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\InvalidPaymentException;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;

class ProcessPaymentRequest
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
	 * @param string $merchantId
	 * @param string $payId
	 */
	public function __construct(
		$merchantId,
		$payId
	)
	{
		Validator::checkPayId($payId);

		$this->merchantId = $merchantId;
		$this->payId = $payId;
	}

	/**
	 * @param ApiClient $apiClient
	 * @return ProcessPaymentResponse
	 */
	public function send(ApiClient $apiClient)
	{
		$response = $apiClient->get(
			'payment/process/{merchantId}/{payId}/{dttm}/{signature}',
			[
				'merchantId' => $this->merchantId,
				'payId' => $this->payId,
			],
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
				'authCode' => null,
			]),
			function (Response $response) {
				// This handles edge case when provided payId is missing or already expired on gateway
				// In this case gateway responds with HTTP 200 and HTML content. Bad API.
				// See https://github.com/csob/paymentgateway/issues/135
				if ($response->getResponseCode()->equalsValue(ResponseCode::S200_OK)) {
					throw new InvalidPaymentException($this, $response, $this->payId);
				}
			}
		);

		return new ProcessPaymentResponse($response->getHeaders()['Location']);
	}

}
