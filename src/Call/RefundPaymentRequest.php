<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Api\ApiClientInterface;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\Validator;
use function array_filter;

class RefundPaymentRequest
{

	public function __construct(
		private string $merchantId,
		private string $payId,
		private ?int $amount = null,
	)
	{
		Validator::checkPayId($payId);
	}

	public function send(ApiClientInterface $apiClient): AuthCodeStatusDetailPaymentResponse
	{
		$requestData = array_filter([
			'merchantId' => $this->merchantId,
			'payId' => $this->payId,
			'amount' => $this->amount,
		], EncodeHelper::filterValueCallback());

		$response = $apiClient->put(
			'payment/refund',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'payId' => null,
				'dttm' => null,
				'amount' => null,
			]),
			new SignatureDataFormatter(AuthCodeStatusDetailPaymentResponse::encodeForSignature()),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return AuthCodeStatusDetailPaymentResponse::createFromResponseData($data);
	}

}
