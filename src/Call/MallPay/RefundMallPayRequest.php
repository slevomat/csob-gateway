<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\MallPay;

use DateTimeImmutable;
use InvalidArgumentException;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Call\PaymentResponse;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\MallPay\OrderItemReference;
use SlevomatCsobGateway\Validator;
use function array_map;

class RefundMallPayRequest
{

	/**
	 * @param OrderItemReference[] $refundedItems
	 */
	public function __construct(
		private string $merchantId,
		private string $payId,
		private ?int $amount,
		private array $refundedItems,
	)
	{
		if ($amount !== null) {
			Validator::checkNumberPositiveOrZero($amount);
		}
		if ($this->refundedItems === []) {
			throw new InvalidArgumentException('Refund has no items.');
		}
	}

	public function send(ApiClient $apiClient): PaymentResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'payId' => $this->payId,
			'refundedItems' => array_map(static fn (OrderItemReference $item): array => $item->encode(), $this->refundedItems),
		];

		if ($this->amount !== null) {
			$requestData['amount'] = $this->amount;
		}

		$response = $apiClient->put(
			'mallpay/refund',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'payId' => null,
				'dttm' => null,
				'amount' => null,
				'refundedItems' => [
					[
						'code' => null,
						'ean' => null,
						'name' => null,
						'type' => null,
						'quantity' => null,
					],
				],
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
