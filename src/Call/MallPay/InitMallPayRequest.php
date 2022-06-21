<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\MallPay;

use DateTimeImmutable;
use InvalidArgumentException;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\MallPay\AddressType;
use SlevomatCsobGateway\MallPay\Customer;
use SlevomatCsobGateway\MallPay\Order;
use SlevomatCsobGateway\Validator;
use function array_key_exists;
use function base64_encode;

class InitMallPayRequest
{

	public function __construct(
		private string $merchantId,
		private string $orderId,
		private Customer $customer,
		private Order $order,
		private bool $agreeTC,
		private string $clientIp,
		private HttpMethod $returnMethod,
		private string $returnUrl,
		private ?string $merchantData = null,
		private ?int $ttlSec = null,
	)
	{
		Validator::checkOrderId($orderId);
		if ($merchantData !== null) {
			Validator::checkMerchantData($merchantData);
		}
		if ($ttlSec !== null) {
			Validator::checkMallPayTtlSec($ttlSec);
		}
		if ($order->getItems() === []) {
			throw new InvalidArgumentException('Order has no items.');
		}
		$hasBillingAddress = false;
		foreach ($order->getAddresses() as $address) {
			if ($address->getAddressType()->equals(AddressType::get(AddressType::BILLING))) {
				$hasBillingAddress = true;
				break;
			}
		}
		if (!$hasBillingAddress) {
			throw new InvalidArgumentException('Order doesnt have billing address.');
		}
		if ($returnMethod->equals(HttpMethod::get(HttpMethod::PUT))) {
			throw new InvalidArgumentException('Unsupported return method PUT.');
		}
	}

	public function send(ApiClient $apiClient): InitMallPayResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'orderNo' => $this->orderId,
			'customer' => $this->customer->encode(),
			'order' => $this->order->encode(),
			'agreeTC' => $this->agreeTC,
			'clientIp' => $this->clientIp,
			'returnUrl' => $this->returnUrl,
			'returnMethod' => $this->returnMethod->getValue(),
		];

		if ($this->merchantData !== null) {
			$requestData['merchantData'] = base64_encode($this->merchantData);
		}
		if ($this->ttlSec !== null) {
			$requestData['ttlSec'] = $this->ttlSec;
		}

		$response = $apiClient->post(
			'mallpay/init',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'orderNo' => null,
				'customer' => [
					'firstName' => null,
					'lastName' => null,
					'fullName' => null,
					'titleBefore' => null,
					'titleAfter' => null,
					'email' => null,
					'phone' => null,
					'tin' => null,
					'vatin' => null,
				],
				'order' => [
					'totalPrice' => [
						'amount' => null,
						'currency' => null,
					],
					'totalVat' => [
						[
							'amount' => null,
							'currency' => null,
							'vatRate' => null,
						],
					],
					'addresses' => [
						[
							'name' => null,
							'country' => null,
							'city' => null,
							'streetAddress' => null,
							'streetNumber' => null,
							'zip' => null,
							'addressType' => null,
						],
					],
					'deliveryType' => null,
					'carrierId' => null,
					'carrierCustom' => null,
					'items' => [
						[
							'code' => null,
							'ean' => null,
							'name' => null,
							'type' => null,
							'quantity' => null,
							'variant' => null,
							'description' => null,
							'producer' => null,
							'categories' => [],
							'unitPrice' => [
								'amount' => null,
								'currency' => null,
							],
							'unitVat' => [
								'amount' => null,
								'currency' => null,
								'vatRate' => null,
							],
							'totalPrice' => [
								'amount' => null,
								'currency' => null,
							],
							'totalVat' => [
								'amount' => null,
								'currency' => null,
								'vatRate' => null,
							],
							'productUrl' => null,
						],
					],
				],
				'agreeTC' => null,
				'dttm' => null,
				'clientIp' => null,
				'returnUrl' => null,
				'returnMethod' => null,
				'merchantData' => null,
				'ttlSec' => null,
			]),
			new SignatureDataFormatter([
				'payId' => null,
				'dttm' => null,
				'resultCode' => null,
				'resultMessage' => null,
				'paymentStatus' => null,
				'mallpayUrl' => null,
			]),
		);

		/** @var mixed[] $data */
		$data = $response->getData();
		$responseDateTime = DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']);

		return new InitMallPayResponse(
			$data['payId'],
			$responseDateTime,
			ResultCode::get($data['resultCode']),
			$data['resultMessage'],
			array_key_exists('paymentStatus', $data) ? PaymentStatus::get($data['paymentStatus']) : null,
			null,
			null,
			[],
			$data['mallpayUrl'] ?? null,
		);
	}

}
