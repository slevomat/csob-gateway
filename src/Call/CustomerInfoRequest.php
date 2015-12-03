<?php

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;

class CustomerInfoRequest
{

	/**
	 * @var string
	 */
	private $merchantId;

	/**
	 * @var string
	 */
	private $customerId;

	/**
	 * @param string $merchantId
	 * @param string $customerId
	 */
	public function __construct(
		$merchantId,
		$customerId
	)
	{
		Validator::checkCustomerId($customerId);

		$this->merchantId = $merchantId;
		$this->customerId = $customerId;
	}

	/**
	 * @param ApiClient $apiClient
	 * @return CustomerInfoResponse
	 */
	public function send(ApiClient $apiClient)
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
			new ResultCode($data['resultCode']),
			$data['resultMessage'],
			isset($data['customerId']) ? $data['customerId'] : null
		);
	}

}
