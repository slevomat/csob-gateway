<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;

class CustomerInfoRequest
{

	/** @var string */
	private $merchantId;

	/** @var string */
	private $customerId;

	public function __construct(
		string $merchantId,
		string $customerId
	)
	{
		Validator::checkCustomerId($customerId);

		$this->merchantId = $merchantId;
		$this->customerId = $customerId;
	}

	public function send(ApiClient $apiClient): CustomerInfoResponse
	{
		$response = $apiClient->get(
			'customer/info/{merchantId}/{customerId}/{dttm}/{signature}',
			[
				'merchantId' => $this->merchantId,
				'customerId' => $this->customerId,
			],
			new SignatureDataFormatter([
				'merchantId' => null,
				'customerId' => null,
				'dttm' => null,
			]),
			new SignatureDataFormatter([
				'customerId' => null,
				'dttm' => null,
				'resultCode' => null,
				'resultMessage' => null,
			])
		);

		$data = $response->getData();

		return new CustomerInfoResponse(
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			ResultCode::get($data['resultCode']),
			$data['resultMessage'],
			$data['customerId'] ?? null
		);
	}

}
