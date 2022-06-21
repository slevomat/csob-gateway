<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\GooglePay;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;

class GooglePayInfoRequest
{

	public function __construct(private string $merchantId)
	{
	}

	public function send(ApiClient $apiClient): GooglePayInfoResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
		];

		$response = $apiClient->post(
			'googlepay/info',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'dttm' => null,
			]),
			new SignatureDataFormatter([
				'dttm' => null,
				'resultCode' => null,
				'resultMessage' => null,
				'checkoutParams' => [
					'apiVersion' => null,
					'apiVersionMinor' => null,
					'allowedCardNetworks' => [],
					'allowedCardAuthMethods' => [],
					'googlePayMerchantId' => null,
					'merchantName' => null,
					'totalPriceStatus' => null,
				],
			]),
		);

		/** @var mixed[] $data */
		$data = $response->getData();
		$responseDateTime = DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']);

		return new GooglePayInfoResponse(
			$responseDateTime,
			ResultCode::from($data['resultCode']),
			$data['resultMessage'] ?? '',
			$data['checkoutParams'] ?? [],
		);
	}

}
