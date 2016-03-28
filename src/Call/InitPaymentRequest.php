<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Cart;
use SlevomatCsobGateway\CartItem;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Language;
use SlevomatCsobGateway\Validator;

class InitPaymentRequest
{

	/**
	 * @var string
	 */
	private $merchantId;

	/**
	 * @var string
	 */
	private $orderId;

	/**
	 * @var PayOperation
	 */
	private $payOperation;

	/**
	 * @var PayMethod
	 */
	private $payMethod;

	/**
	 * @var bool
	 */
	private $closePayment;

	/**
	 * @var string
	 */
	private $returnUrl;

	/**
	 * @var HttpMethod
	 */
	private $returnMethod;

	/**
	 * @var Cart
	 */
	private $cart;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var string|null
	 */
	private $merchantData;

	/**
	 * @var string|null
	 */
	private $customerId;

	/**
	 * @var Language|null
	 */
	private $language;

	public function __construct(
		string $merchantId,
		string $orderId,
		PayOperation $payOperation,
		PayMethod $payMethod,
		bool $closePayment,
		string $returnUrl,
		HttpMethod $returnMethod,
		Cart $cart,
		string $description,
		string $merchantData = null,
		string $customerId = null,
		Language $language = null
	)
	{
		Validator::checkOrderId($orderId);
		Validator::checkReturnUrl($returnUrl);
		Validator::checkDescription($description);
		if ($merchantData !== null) {
			Validator::checkMerchantData($merchantData);
		}
		if ($customerId !== null) {
			Validator::checkCustomerId($customerId);
		}

		$this->merchantId = $merchantId;
		$this->orderId = $orderId;
		$this->payOperation = $payOperation;
		$this->payMethod = $payMethod;
		$this->closePayment = $closePayment;
		$this->returnUrl = $returnUrl;
		$this->returnMethod = $returnMethod;
		$this->cart = $cart;
		$this->description = $description;
		$this->merchantData = $merchantData;
		$this->customerId = $customerId;
		$this->language = $language;
	}

	public function send(ApiClient $apiClient): PaymentResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'orderNo' => $this->orderId,
			'payOperation' => $this->payOperation->getValue(),
			'payMethod' => $this->payMethod->getValue(),
			'totalAmount' => $this->cart->countTotalAmount(),
			'currency' => $this->cart->getCurrency()->getValue(),
			'closePayment' => $this->closePayment,
			'returnUrl' => $this->returnUrl,
			'returnMethod' => $this->returnMethod->getValue(),
			'cart' => array_map(function (CartItem $cartItem) {
				$cartItemValues = [
					'name' => $cartItem->getName(),
					'quantity' => $cartItem->getQuantity(),
					'amount' => $cartItem->getAmount(),
				];

				if ($cartItem->getDescription() !== null) {
					$cartItemValues['description'] = $cartItem->getDescription();
				}

				return $cartItemValues;

			}, $this->cart->getItems()),
			'description' => $this->description,
		];

		if ($this->merchantData !== null) {
			$requestData['merchantData'] = base64_encode($this->merchantData);
		}

		if ($this->customerId !== null) {
			$requestData['customerId'] = $this->customerId;
		}

		if ($this->language !== null) {
			$requestData['language'] = $this->language->getValue();
		}

		$response = $apiClient->post(
			'payment/init',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'orderNo' => null,
				'dttm' => null,
				'payOperation' => null,
				'payMethod' => null,
				'totalAmount' => null,
				'currency' => null,
				'closePayment' => null,
				'returnUrl' => null,
				'returnMethod' => null,
				'cart' => [
					'name' => null,
					'quantity' => null,
					'amount' => null,
					'description' => null,
				],
				'description' => null,
				'merchantData' => null,
				'customerId' => null,
				'language' => null,
			]),
			new SignatureDataFormatter([
				'payId' => null,
				'dttm' => null,
				'resultCode' => null,
				'resultMessage' => null,
				'paymentStatus' => null,
				'authCode' => null,
			])
		);

		$data = $response->getData();

		return new PaymentResponse(
			$data['payId'],
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			new ResultCode($data['resultCode']),
			$data['resultMessage'],
			array_key_exists('paymentStatus', $data) ? new PaymentStatus($data['paymentStatus']) : null,
			$data['authCode'] ?? null
		);
	}

}
