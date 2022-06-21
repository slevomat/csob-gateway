<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Masterpass;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Call\PaymentResponse;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;

class BasicFinishRequest
{

	/**
	 * @param mixed[] $callbackParams
	 */
	public function __construct(
		private string $merchantId,
		private string $payId,
		private array $callbackParams,
	)
	{
		Validator::checkPayId($payId);
	}

	public function send(ApiClient $apiClient): PaymentResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'payId' => $this->payId,
			'callbackParams' => $this->callbackParams,
		];

		$response = $apiClient->post(
			'masterpass/basic/finish',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'payId' => null,
				'dttm' => null,
				'callbackParams' => [
					'mpstatus' => null,
					'oauthToken' => null,
					'checkoutResourceUrl' => null,
					'oauthVerifier' => null,
				],
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

		return new PaymentResponse(
			$data['payId'],
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			ResultCode::from($data['resultCode']),
			$data['resultMessage'],
			isset($data['paymentStatus']) ? PaymentStatus::from($data['paymentStatus']) : null,
		);
	}

}
