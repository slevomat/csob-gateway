<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\GooglePay;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Call\PaymentResponse;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Price;
use SlevomatCsobGateway\Validator;
use function base64_encode;

class InitGooglePayRequest
{

	/** @var string */
	private $merchantId;

	/** @var string */
	private $orderId;

	/** @var string|null */
	private $clientIp;

	/** @var Price */
	private $totalPrice;

	/** @var bool */
	private $closePayment;

	/** @var string|null */
	private $merchantData;

	public function __construct(
		string $merchantId,
		string $orderId,
		string $clientIp,
		Price $totalPrice,
		bool $closePayment,
		?string $merchantData
	)
	{
		Validator::checkOrderId($orderId);
		if ($merchantData !== null) {
			Validator::checkMerchantData($merchantData);
		}

		$this->merchantId = $merchantId;
		$this->orderId = $orderId;
		$this->clientIp = $clientIp;
		$this->totalPrice = $totalPrice;
		$this->closePayment = $closePayment;
		$this->merchantData = $merchantData;
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

		$response = $apiClient->post(
			'googlepay/init',
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
