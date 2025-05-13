<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;

class EchoCustomerRequest
{

	public function __construct(private string $merchantId, private string $customerId)
	{
		Validator::checkCustomerId($customerId);
	}

	public function send(ApiClient $apiClient): EchoCustomerResponse
	{
		$response = $apiClient->post(
			'echo/customer',
			[
				'merchantId' => $this->merchantId,
				'customerId' => $this->customerId,
			],
			new SignatureDataFormatter([
				'merchantId' => null,
				'customerId' => null,
				'dttm' => null,
			]),
			new SignatureDataFormatter(EchoCustomerResponse::encodeForSignature()),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return EchoCustomerResponse::createFromResponseData($data);
	}

}
