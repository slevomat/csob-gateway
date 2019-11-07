<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\ApplePay;

use DateTimeImmutable;
use Nette\Utils\Json;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Call\PaymentResponse;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;
use function base64_encode;

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
		$requestData = [
			'merchantId' => $this->merchantId,
			'payId' => $this->payId,
			'payload' => base64_encode(Json::encode($this->payload)),
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
