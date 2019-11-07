<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\OneClick;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Call\PaymentResponse;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;

class OneClickEchoRequest
{

	/** @var string */
	private $merchantId;

	/** @var string */
	private $origPayId;

	public function __construct(string $merchantId, string $origPayId)
	{
		$this->merchantId = $merchantId;
		$this->origPayId = $origPayId;
	}

	public function send(ApiClient $apiClient): PaymentResponse
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
			new SignatureDataFormatter([
				'origPayId' => null,
				'dttm' => null,
				'resultCode' => null,
				'resultMessage' => null,
			])
		);

		$data = $response->getData();

		return new PaymentResponse(
			$data['origPayId'],
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			ResultCode::get($data['resultCode']),
			$data['resultMessage'],
			null
		);
	}

}
