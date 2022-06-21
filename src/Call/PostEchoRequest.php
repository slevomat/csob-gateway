<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;

class PostEchoRequest
{

	public function __construct(private string $merchantId)
	{
	}

	public function send(ApiClient $apiClient): EchoResponse
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

		return new EchoResponse(
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			ResultCode::from($data['resultCode']),
			$data['resultMessage'],
		);
	}

}
