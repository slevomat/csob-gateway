<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\MallPay;

use InvalidArgumentException;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\MallPay\AddressType;
use SlevomatCsobGateway\MallPay\Customer;
use SlevomatCsobGateway\MallPay\Order;
use SlevomatCsobGateway\Validator;
use function array_filter;
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
		Validator::checkReturnMethod($returnMethod);
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
			if ($address->getAddressType() === AddressType::BILLING) {
				$hasBillingAddress = true;
				break;
			}
		}
		if (!$hasBillingAddress) {
			throw new InvalidArgumentException('Order doesnt have billing address.');
		}
		if ($returnMethod === HttpMethod::PUT) {
			throw new InvalidArgumentException('Unsupported return method PUT.');
		}
	}

	public function send(ApiClient $apiClient): InitMallPayResponse
	{
		$requestData = array_filter([
			'merchantId' => $this->merchantId,
			'orderNo' => $this->orderId,
			'customer' => $this->customer->encode(),
			'order' => $this->order->encode(),
			'agreeTC' => $this->agreeTC,
			'clientIp' => $this->clientIp,
			'returnUrl' => $this->returnUrl,
			'returnMethod' => $this->returnMethod->value,
			'merchantData' => $this->merchantData !== null ? base64_encode($this->merchantData) : null,
			'ttlSec' => $this->ttlSec,
		], EncodeHelper::filterValueCallback());

		$response = $apiClient->post(
			'mallpay/init',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'orderNo' => null,
				'customer' => Customer::encodeForSignature(),
				'order' => Order::encodeForSignature(),
				'agreeTC' => null,
				'dttm' => null,
				'clientIp' => null,
				'returnUrl' => null,
				'returnMethod' => null,
				'merchantData' => null,
				'ttlSec' => null,
			]),
			new SignatureDataFormatter(InitMallPayResponse::encodeForSignature()),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return InitMallPayResponse::createFromResponseData($data);
	}

}
