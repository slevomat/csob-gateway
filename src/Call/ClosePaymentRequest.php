<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Api\ApiClientInterface;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\Validator;
use function array_filter;

class ClosePaymentRequest
{

	public function __construct(
		private string $merchantId,
		private string $payId,
		private ?int $totalAmount = null,
	)
	{
		Validator::checkPayId($payId);
	}

	public function send(ApiClientInterface $apiClient): AuthCodeStatusDetailPaymentResponse
	{
		$data = array_filter([
			'merchantId' => $this->merchantId,
			'payId' => $this->payId,
			'totalAmount' => $this->totalAmount,
		], EncodeHelper::filterValueCallback());

		$response = $apiClient->put(
			'payment/close',
			$data,
			new SignatureDataFormatter([
				'merchantId' => null,
				'payId' => null,
				'dttm' => null,
				'totalAmount' => null,
			]),
			new SignatureDataFormatter(AuthCodeStatusDetailPaymentResponse::encodeForSignature()),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return AuthCodeStatusDetailPaymentResponse::createFromResponseData($data);
	}

}
