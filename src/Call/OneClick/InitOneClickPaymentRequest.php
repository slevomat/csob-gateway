<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\OneClick;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Call\PaymentResponse;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Price;
use SlevomatCsobGateway\Validator;
use function base64_encode;

class InitOneClickPaymentRequest
{

	public function __construct(
		private string $merchantId,
		private string $origPayId,
		private string $orderId,
		private string $clientIp,
		private ?Price $price = null,
		private ?string $description = null,
		private ?string $merchantData = null,
	)
	{
		Validator::checkPayId($origPayId);
		Validator::checkOrderId($orderId);
		if ($description !== null) {
			Validator::checkDescription($description);
		}
		if ($merchantData !== null) {
			Validator::checkMerchantData($merchantData);
		}
	}

	public function send(ApiClient $apiClient): PaymentResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'origPayId' => $this->origPayId,
			'orderNo' => $this->orderId,
			'clientIp' => $this->clientIp,
		];

		if ($this->price !== null) {
			$requestData['totalAmount'] = $this->price->getAmount();
			$requestData['currency'] = $this->price->getCurrency()->value;
		}

		if ($this->description !== null) {
			$requestData['description'] = $this->description;
		}

		if ($this->merchantData !== null) {
			$requestData['merchantData'] = base64_encode($this->merchantData);
		}

		$response = $apiClient->post(
			'oneclick/init',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'origPayId' => null,
				'orderNo' => null,
				'dttm' => null,
				'clientIp' => null,
				'totalAmount' => null,
				'currency' => null,
				'description' => null,
				'merchantData' => null,
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
