<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\GooglePay;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Call\InvalidJsonPayloadException;
use SlevomatCsobGateway\Call\PaymentResponse;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;
use function base64_encode;

class StartGooglePayRequest
{

	/** @var string */
	private $merchantId;

	/** @var string */
	private $payId;

	/** @var array|mixed[] */
	private $payload;

	/**
	 * @param string $merchantId
	 * @param string $payId
	 * @param mixed[] $payload Complete payload from Google Pay JS API, containing paymentMethodData.tokenizationData.token
	 */
	public function __construct(
		string $merchantId,
		string $payId,
		array $payload
	)
	{
		Validator::checkPayId($payId);

		$this->merchantId = $merchantId;
		$this->payId = $payId;
		$this->payload = $payload;
	}

	public function send(ApiClient $apiClient): PaymentResponse
	{
		$payloadData = $this->payload['paymentMethodData']['tokenizationData']['token'] ?? null;
		if ($payloadData === null) {
			throw new InvalidJsonPayloadException('Missing token in Google Pay payload.');
		}
		$requestData = [
			'merchantId' => $this->merchantId,
			'payId' => $this->payId,
			'payload' => base64_encode((string) $payloadData),
		];

		$response = $apiClient->post(
			'googlepay/start',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'payId' => null,
				'payload' => null,
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
