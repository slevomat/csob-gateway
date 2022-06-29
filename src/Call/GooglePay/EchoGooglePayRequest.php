<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\GooglePay;

use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;

class EchoGooglePayRequest
{

	public function __construct(private string $merchantId)
	{
	}

	public function send(ApiClient $apiClient): EchoGooglePayResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
		];

		$response = $apiClient->post(
			'googlepay/echo',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'dttm' => null,
			]),
			new SignatureDataFormatter(EchoGooglePayResponse::encodeForSignature()),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return EchoGooglePayResponse::createFromResponseData($data);
	}

}
