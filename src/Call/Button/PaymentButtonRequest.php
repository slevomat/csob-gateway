<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Button;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Language;
use SlevomatCsobGateway\Price;
use SlevomatCsobGateway\Validator;

class PaymentButtonRequest
{

	/** @var string */
	private $merchantId;

	/** @var string */
	private $orderId;

	/** @var string */
	private $clientIp;

	/** @var Price */
	private $totalPrice;

	/** @var string */
	private $returnUrl;

	/** @var HttpMethod */
	private $returnMethod;

	/** @var PaymentButtonBrand */
	private $brand;

	/** @var string|null */
	private $merchantData;

	/** @var Language */
	private $language;

	public function __construct(
		string $merchantId,
		string $orderId,
		string $clientIp,
		Price $totalPrice,
		string $returnUrl,
		HttpMethod $returnMethod,
		PaymentButtonBrand $brand,
		?string $merchantData,
		Language $language
	)
	{
		Validator::checkReturnUrl($returnUrl);
		if ($merchantData !== null) {
			Validator::checkMerchantData($merchantData);
		}

		$this->merchantId = $merchantId;
		$this->orderId = $orderId;
		$this->clientIp = $clientIp;
		$this->totalPrice = $totalPrice;
		$this->returnUrl = $returnUrl;
		$this->returnMethod = $returnMethod;
		$this->brand = $brand;
		$this->merchantData = $merchantData;
		$this->language = $language;
	}

	public function send(ApiClient $apiClient): PaymentButtonResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'orderNo' => $this->orderId,
			'clientIp' => $this->clientIp,
			'totalAmount' => $this->totalPrice->getAmount(),
			'currency' => $this->totalPrice->getCurrency()->getValue(),
			'returnUrl' => $this->returnUrl,
			'returnMethod' => $this->returnMethod->getValue(),
			'brand' => $this->brand->getValue(),
			'language' => $this->language->getValue(),
		];

		if ($this->merchantData !== null) {
			$requestData['merchantData'] = $this->merchantData;
		}

		$response = $apiClient->post(
			'button/init',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'orderNo' => null,
				'dttm' => null,
				'clientIp' => null,
				'totalAmount' => null,
				'currency' => null,
				'returnUrl' => null,
				'returnMethod' => null,
				'brand' => null,
				'merchantData' => null,
				'language' => null,
			]),
			new SignatureDataFormatter([
				'payId' => null,
				'dttm' => null,
				'resultCode' => null,
				'resultMessage' => null,
				'paymentStatus' => null,
				'redirect' => [
					'method' => null,
					'url' => null,
					'params' => null,
				],
			])
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		$redirectUrl = null;
		$redirectMethod = null;
		$redirectParams = [];
		if (isset($data['redirect'])) {
			$redirectUrl = $data['redirect']['url'];
			$redirectMethod = HttpMethod::get($data['redirect']['method']);
			$redirectParams = $data['redirect']['params'] ?? null;
		}

		return new PaymentButtonResponse(
			$data['payId'],
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			ResultCode::get($data['resultCode']),
			$data['resultMessage'],
			isset($data['paymentStatus']) ? PaymentStatus::get($data['paymentStatus']) : null,
			$redirectMethod,
			$redirectUrl,
			$redirectParams
		);
	}

}
