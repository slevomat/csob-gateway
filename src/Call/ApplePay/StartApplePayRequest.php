<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\ApplePay;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Call\PaymentResponse;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;
use const JSON_ERROR_NONE;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;
use function base64_encode;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;

class StartApplePayRequest
{

	/** @var string */
	private $merchantId;

	/** @var string */
	private $payId;

	/** @var array|mixed[] */
	private $payload;

	/** @var int|null */
	private $totalAmount;

	/**
	 * @param string $merchantId
	 * @param string $payId
	 * @param mixed[] $payload
	 * @param int|null $totalAmount
	 */
	public function __construct(
		string $merchantId,
		string $payId,
		array $payload,
		?int $totalAmount
	)
	{
		Validator::checkPayId($payId);

		$this->merchantId = $merchantId;
		$this->payId = $payId;
		$this->payload = $payload;
		$this->totalAmount = $totalAmount;
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
			])
		);

		/** @var mixed[] $data */
		$data = $response->getData();
		$responseDateTime = DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']);

		return new PaymentResponse(
			$data['payId'],
			$responseDateTime,
			ResultCode::get($data['resultCode']),
			$data['resultMessage'],
			isset($data['paymentStatus']) ? PaymentStatus::get($data['paymentStatus']) : null
		);
	}

}
