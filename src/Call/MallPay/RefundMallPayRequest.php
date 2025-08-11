<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\MallPay;

use InvalidArgumentException;
use SlevomatCsobGateway\Api\ApiClientInterface;
use SlevomatCsobGateway\Call\StatusDetailPaymentResponse;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\MallPay\OrderItemReference;
use SlevomatCsobGateway\Validator;
use function array_filter;
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

	public function send(ApiClientInterface $apiClient): StatusDetailPaymentResponse
	{
		$requestData = array_filter([
			'merchantId' => $this->merchantId,
			'payId' => $this->payId,
			'amount' => $this->amount,
			'refundedItems' => array_map(static fn (OrderItemReference $item): array => $item->encode(), $this->refundedItems),
		], EncodeHelper::filterValueCallback());

		$response = $apiClient->put(
			'mallpay/refund',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'payId' => null,
				'dttm' => null,
				'amount' => null,
				'refundedItems' => [
					OrderItemReference::encodeForSignature(),
				],
			]),
			new SignatureDataFormatter(StatusDetailPaymentResponse::encodeForSignature()),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return StatusDetailPaymentResponse::createFromResponseData($data);
	}

}
