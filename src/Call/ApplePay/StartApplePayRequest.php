<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\ApplePay;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Call\InvalidJsonPayloadException;
use SlevomatCsobGateway\Call\PaymentResponse;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;
use function base64_encode;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use const JSON_ERROR_NONE;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class StartApplePayRequest
{

	/**
	 * @param mixed[] $payload
	 */
	public function __construct(
		private string $merchantId,
		private string $payId,
		private array $payload,
		private ?int $totalAmount = null,
	)
	{
		Validator::checkPayId($payId);
	}

	public function send(ApiClient $apiClient): PaymentResponse
	{
		$payloadData = json_encode($this->payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		$error = json_last_error();
		if ($error !== JSON_ERROR_NONE) {
			throw new InvalidJsonPayloadException(json_last_error_msg(), $error);
		}
		$requestData = [
			'merchantId' => $this->merchantId,
			'payId' => $this->payId,
			'payload' => base64_encode((string) $payloadData),
		];

		if ($this->totalAmount !== null) {
			$requestData['totalAmount'] = $this->totalAmount;
		}

		$response = $apiClient->post(
			'applepay/start',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'payId' => null,
				'payload' => null,
				'totalAmount' => null,
				'dttm' => null,
			]),
			new SignatureDataFormatter([
				'payId' => null,
				'dttm' => null,
				'resultCode' => null,
				'resultMessage' => null,
				'paymentStatus' => null,
			]),
		);

		/** @var mixed[] $data */
		$data = $response->getData();
		$responseDateTime = DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']);

		return new PaymentResponse(
			$data['payId'],
			$responseDateTime,
			ResultCode::from($data['resultCode']),
			$data['resultMessage'],
			isset($data['paymentStatus']) ? PaymentStatus::from($data['paymentStatus']) : null,
		);
	}

}
