<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Price;
use SlevomatCsobGateway\Validator;

class OneclickInitPaymentRequest
{

	/**
	 * @var string
	 */
	private $merchantId;

	/**
	 * @var string
	 */
	private $origPayId;

	/**
	 * @var string
	 */
	private $orderId;

	/**
	 * @var Price|null
	 */
	private $price;

	/**
	 * @var string|null
	 */
	private $description;

	public function __construct(
		string $merchantId,
		string $origPayId,
		string $orderId,
		Price $price = null,
		string $description = null
	)
	{
		Validator::checkPayId($origPayId);
		Validator::checkOrderId($orderId);
		if ($description !== null) {
			Validator::checkDescription($description);
		}

		$this->merchantId = $merchantId;
		$this->origPayId = $origPayId;
		$this->orderId = $orderId;
		$this->price = $price;
		$this->description = $description;
	}

	public function send(ApiClient $apiClient): PaymentResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'origPayId' => $this->origPayId,
			'orderNo' => $this->orderId,
		];

		if ($this->price !== null) {
			$requestData['totalAmount'] = $this->price->getAmount();
			$requestData['currency'] = $this->price->getCurrency()->getValue();
		}

		if ($this->description !== null) {
			$requestData['description'] = $this->description;
		}

		$response = $apiClient->post(
			'payment/oneclick/init',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'origPayId' => null,
				'orderNo' => null,
				'dttm' => null,
				'totalAmount' => null,
				'currency' => null,
				'description' => null,
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

		return new PaymentResponse(
			$data['payId'],
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			new ResultCode($data['resultCode']),
			$data['resultMessage'],
			isset($data['paymentStatus']) ? new PaymentStatus($data['paymentStatus']) : null
		);
	}

}
