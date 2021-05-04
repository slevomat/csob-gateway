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

	/** @var string */
	private $merchantId;

	/** @var string */
	private $payId;

	/** @var int|null */
	private $amount;

	/** @var OrderItemReference[] */
	private $refundedItems;

	/**
	 * @param string $merchantId
	 * @param string $payId
	 * @param int|null $amount
	 * @param OrderItemReference[] $refundedItems
	 */
	public function __construct(
		string $merchantId,
		string $payId,
		?int $amount,
		array $refundedItems
	)
	{
		if ($amount !== null) {
			Validator::checkNumberPositiveOrZero($amount);
		}
		if ($this->refundedItems === []) {
			throw new InvalidArgumentException('Refund has no items.');
		}

		$this->merchantId = $merchantId;
		$this->payId = $payId;
		$this->amount = $amount;
		$this->refundedItems = $refundedItems;
	}

	public function send(ApiClient $apiClient): PaymentResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'payId' => $this->payId,
			'refundedItems' => array_map(static function (OrderItemReference $item): array {
				return $item->encode();
			}, $this->refundedItems),
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
