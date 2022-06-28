<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\ApplePay;

use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;

class EchoApplePayRequest
{

	public function __construct(private string $merchantId)
	{
	}

	public function send(ApiClient $apiClient): EchoApplePayResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
		];

		$response = $apiClient->post(
			'applepay/echo',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'dttm' => null,
			]),
			new SignatureDataFormatter(EchoApplePayResponse::encodeForSignature()),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return EchoApplePayResponse::createFromResponseData($data);
	}

}
