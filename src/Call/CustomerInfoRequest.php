<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;

class CustomerInfoRequest
{

	public function __construct(private string $merchantId, private string $customerId)
	{
		Validator::checkCustomerId($customerId);
	}

	public function send(ApiClient $apiClient): CustomerInfoResponse
	{
		$response = $apiClient->get(
			'customer/echo/{merchantId}/{customerId}/{dttm}/{signature}',
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
			]),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return new CustomerInfoResponse(
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			ResultCode::get($data['resultCode']),
			$data['resultMessage'],
			$data['customerId'] ?? null,
		);
	}

}
