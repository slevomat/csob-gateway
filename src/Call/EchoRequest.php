<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;

class EchoRequest
{

	public function __construct(private string $merchantId)
	{
	}

	public function send(ApiClient $apiClient): EchoResponse
	{
		$response = $apiClient->get(
			'echo/{merchantId}/{dttm}/{signature}',
			[
				'merchantId' => $this->merchantId,
			],
			new SignatureDataFormatter([
				'merchantId' => null,
				'dttm' => null,
			]),
			new SignatureDataFormatter(EchoResponse::encodeForSignature()),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return EchoResponse::createFromResponseData($data);
	}

}
