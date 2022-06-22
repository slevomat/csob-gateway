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

	public function __construct(
		private string $merchantId,
		private string $orderId,
		private PayOperation $payOperation,
		private PayMethod $payMethod,
		private bool $closePayment,
		private string $returnUrl,
		private HttpMethod $returnMethod,
		private Cart $cart,
		private ?string $merchantData,
		private ?string $customerId,
		private Language $language,
		private ?int $ttlSec = null,
		private ?int $logoVersion = null,
		private ?int $colorSchemeVersion = null,
		private ?DateTimeImmutable $customExpiry = null,
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

		if ($payOperation === PayOperation::CUSTOM_PAYMENT && $customExpiry === null) {
			throw new InvalidArgumentException(sprintf('Custom expiry parameter is required for custom payment.'));
		}
	}

	public function send(ApiClient $apiClient): PaymentResponse
	{
		$price = $this->cart->getCurrentPrice();

		$requestData = [
			'merchantId' => $this->merchantId,
			'orderNo' => $this->orderId,
			'payOperation' => $this->payOperation->value,
			'payMethod' => $this->payMethod->value,
			'totalAmount' => $price->getAmount(),
			'currency' => $price->getCurrency()->value,
			'closePayment' => $this->closePayment,
			'returnUrl' => $this->returnUrl,
			'returnMethod' => $this->returnMethod->value,
			'cart' => array_map(static function (CartItem $cartItem): array {
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
			'language' => $this->language->value,
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
			]),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return new InitPaymentResponse(
			$data['payId'],
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			ResultCode::from($data['resultCode']),
			$data['resultMessage'],
			isset($data['paymentStatus']) ? PaymentStatus::from($data['paymentStatus']) : null,
			$data['authCode'] ?? null,
			null,
			$data['customerCode'] ?? null,
		);
	}

}
