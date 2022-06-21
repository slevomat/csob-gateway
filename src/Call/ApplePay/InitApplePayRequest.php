<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\ApplePay;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Call\PaymentResponse;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Price;
use SlevomatCsobGateway\Validator;
use function base64_encode;

class InitApplePayRequest
{

	private ?string $clientIp = null;

	public function __construct(
		private string $merchantId,
		private string $orderId,
		string $clientIp,
		private Price $totalPrice,
		private bool $closePayment,
		private ?string $merchantData = null,
		private ?int $ttlSec = null,
	)
	{
		Validator::checkOrderId($orderId);
		if ($merchantData !== null) {
			Validator::checkMerchantData($merchantData);
		}
		if ($ttlSec !== null) {
			Validator::checkTtlSec($ttlSec);
		}

		$this->clientIp = $clientIp;
	}

	public function send(ApiClient $apiClient): PaymentResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'orderNo' => $this->orderId,
			'totalAmount' => $this->totalPrice->getAmount(),
			'currency' => $this->totalPrice->getCurrency()->getValue(),
			'closePayment' => $this->closePayment,
			'clientIp' => $this->clientIp,
		];

		if ($this->merchantData !== null) {
			$requestData['merchantData'] = base64_encode($this->merchantData);
		}

		if ($this->ttlSec !== null) {
			$requestData['ttlSec'] = $this->ttlSec;
		}

		$response = $apiClient->post(
			'applepay/init',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'orderNo' => null,
				'dttm' => null,
				'clientIp' => null,
				'totalAmount' => null,
				'currency' => null,
				'closePayment' => null,
				'merchantData' => null,
				'ttlSec' => null,
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
			ResultCode::get($data['resultCode']),
			$data['resultMessage'],
			isset($data['paymentStatus']) ? PaymentStatus::get($data['paymentStatus']) : null,
		);
	}

}
