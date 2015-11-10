<?php

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;

class PostEchoRequest
{

	/**
	 * @var string
	 */
	private $merchantId;

	/**
	 * @param string $merchantId
	 */
	public function __construct(
		$merchantId
	)
	{
		$this->merchantId = $merchantId;
	}

	/**
	 * @param ApiClient $apiClient
	 * @return EchoResponse
	 */
	public function send(ApiClient $apiClient)
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
			])
		);

		$data = $response->getData();

		return new EchoResponse(
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			new ResultCode($data['resultCode']),
			$data['resultMessage']
		);
	}

}
