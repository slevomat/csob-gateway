<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use InvalidArgumentException;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Cart;
use SlevomatCsobGateway\CartItem;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Language;
use SlevomatCsobGateway\Validator;
use function array_map;
use function base64_encode;
use function sprintf;

class InitPaymentRequest
{

	/** @var string */
	private $merchantId;

	/** @var string */
	private $orderId;

	/** @var PayOperation */
	private $payOperation;

	/** @var PayMethod */
	private $payMethod;

	/** @var bool */
	private $closePayment;

	/** @var string */
	private $returnUrl;

	/** @var HttpMethod */
	private $returnMethod;

	/** @var Cart */
	private $cart;

	/** @var string|null */
	private $merchantData;

	/** @var string|null */
	private $customerId;

	/** @var Language */
	private $language;

	/** @var int|null */
	private $ttlSec;

	/** @var int|null */
	private $logoVersion;

	/** @var int|null */
	private $colorSchemeVersion;

	/** @var \DateTimeImmutable|null */
	private $customExpiry;

	public function __construct(
		string $merchantId,
		string $orderId,
		PayOperation $payOperation,
		PayMethod $payMethod,
		bool $closePayment,
		string $returnUrl,
		HttpMethod $returnMethod,
		Cart $cart,
		?string $merchantData,
		?string $customerId,
		Language $language,
		?int $ttlSec = null,
		?int $logoVersion = null,
		?int $colorSchemeVersion = null,
		?DateTimeImmutable $customExpiry = null
	)
	{
		Validator::checkOrderId($orderId);
		Validator::checkReturnUrl($returnUrl);
		if ($merchantData !== null) {
			Validator::checkMerchantData($merchantData);
		}
		if ($customerId !== null) {
			Validator::checkCustomerId($customerId);
		}
		if ($ttlSec !== null) {
			Validator::checkTtlSec($ttlSec);
		}

		if ($payOperation->equals(PayOperation::get(PayOperation::CUSTOM_PAYMENT)) && $customExpiry === null) {
			throw new InvalidArgumentException(sprintf('Custom expiry parameter is required for custom payment.'));
		}

		$this->merchantId = $merchantId;
		$this->orderId = $orderId;
		$this->payOperation = $payOperation;
		$this->payMethod = $payMethod;
		$this->closePayment = $closePayment;
		$this->returnUrl = $returnUrl;
		$this->returnMethod = $returnMethod;
		$this->cart = $cart;
		$this->merchantData = $merchantData;
		$this->customerId = $customerId;
		$this->language = $language;
		$this->ttlSec = $ttlSec;
		$this->logoVersion = $logoVersion;
		$this->colorSchemeVersion = $colorSchemeVersion;
		$this->customExpiry = $customExpiry;
	}

	public function send(ApiClient $apiClient): PaymentResponse
	{
		$price = $this->cart->getCurrentPrice();

		$requestData = [
			'merchantId' => $this->merchantId,
			'orderNo' => $this->orderId,
			'payOperation' => $this->payOperation->getValue(),
			'payMethod' => $this->payMethod->getValue(),
			'totalAmount' => $price->getAmount(),
			'currency' => $price->getCurrency()->getValue(),
			'closePayment' => $this->closePayment,
			'returnUrl' => $this->returnUrl,
			'returnMethod' => $this->returnMethod->getValue(),
			'cart' => array_map(static function (CartItem $cartItem) {
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
			'language' => $this->language->getValue(),
		];

		if ($this->merchantData !== null) {
			$requestData['merchantData'] = base64_encode($this->merchantData);
		}

		if ($this->customerId !== null) {
			$requestData['customerId'] = $this->customerId;
		}

		if ($this->ttlSec !== null) {
			$requestData['ttlSec'] = $this->ttlSec;
		}

		if ($this->logoVersion !== null) {
			$requestData['logoVersion'] = $this->logoVersion;
		}

		if ($this->colorSchemeVersion !== null) {
			$requestData['colorSchemeVersion'] = $this->colorSchemeVersion;
		}

		if ($this->customExpiry !== null) {
			$requestData['customExpiry'] = $this->customExpiry->format('YmdHis');
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
					[
						'name' => null,
						'quantity' => null,
						'amount' => null,
						'description' => null,
					],
				],
				'merchantData' => null,
				'customerId' => null,
				'language' => null,
				'ttlSec' => null,
				'logoVersion' => null,
				'colorSchemeVersion' => null,
				'customExpiry' => null,
			]),
			new SignatureDataFormatter([
				'payId' => null,
				'dttm' => null,
				'resultCode' => null,
				'resultMessage' => null,
				'paymentStatus' => null,
				'authCode' => null,
				'customerCode' => null,
			])
		);

		$data = $response->getData();

		return new InitPaymentResponse(
			$data['payId'],
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			ResultCode::get($data['resultCode']),
			$data['resultMessage'],
			isset($data['paymentStatus']) ? PaymentStatus::get($data['paymentStatus']) : null,
			$data['authCode'] ?? null,
			null,
			$data['customerCode'] ?? null
		);
	}

}
