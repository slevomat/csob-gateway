<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\OneClick;

use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;

class EchoOneClickRequest
{

	public function __construct(private string $merchantId, private string $origPayId)
	{
	}

	public function send(ApiClient $apiClient): EchoOneClickResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'origPayId' => $this->origPayId,
		];

		$response = $apiClient->post(
			'oneclick/echo',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'origPayId' => null,
				'dttm' => null,
			]),
			new SignatureDataFormatter(EchoOneClickResponse::encodeForSignature()),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return EchoOneClickResponse::createFromResponseData($data);
	}

}
