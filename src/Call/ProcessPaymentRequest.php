<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\InvalidPaymentException;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;
use function array_change_key_case;

class ProcessPaymentRequest
{

	public function __construct(private string $merchantId, private string $payId)
	{
		Validator::checkPayId($payId);
	}

	public function send(ApiClient $apiClient): ProcessPaymentResponse
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
			new SignatureDataFormatter(ProcessPaymentResponse::encodeForSignature()),
			function (Response $response): void {
				// This handles edge case when provided payId is missing or already expired on gateway
				// In this case gateway responds with HTTP 200 and HTML content. Bad API.
				// See https://github.com/csob/paymentgateway/issues/135
				if ($response->getResponseCode() === ResponseCode::S200_OK) {
					throw new InvalidPaymentException($this, $response, $this->payId);
				}
			},
		);

		$headers = array_change_key_case($response->getHeaders());
		/** @var string $gatewayLocationUrl */
		$gatewayLocationUrl = $headers['location'];

		return new ProcessPaymentResponse($gatewayLocationUrl);
	}

}
