<?php declare(strict_types = 1);

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

	public function __construct(
		string $merchantId
	)
	{
		$this->merchantId = $merchantId;
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
			])
		);

		$data = $response->getData();

		return new EchoResponse(
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			ResultCode::get($data['resultCode']),
			$data['resultMessage']
		);
	}

}
