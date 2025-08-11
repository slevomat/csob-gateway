<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Api\ApiClientInterface;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;

class PostEchoRequest
{

	public function __construct(private string $merchantId)
	{
	}

	public function send(ApiClientInterface $apiClient): EchoResponse
	{
		$response = $apiClient->post(
			'echo',
			[
				'merchantId' => $this->merchantId,
			],
			new SignatureDataFormatter([
				'merchantId' => null,
				'dttm' => null,
			]),
			new SignatureDataFormatter([
				'dttm' => null,
				'resultCode' => null,
				'resultMessage' => null,
			]),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return EchoResponse::createFromResponseData($data);
	}

}
