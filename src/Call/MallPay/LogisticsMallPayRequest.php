<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\MallPay;

use DateTimeImmutable;
use InvalidArgumentException;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Call\StatusDetailPaymentResponse;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\MallPay\LogisticsEvent;
use SlevomatCsobGateway\MallPay\OrderReference;
use function array_filter;

class LogisticsMallPayRequest
{

	public function __construct(
		private string $merchantId,
		private string $payId,
		private LogisticsEvent $event,
		private DateTimeImmutable $date,
		private OrderReference $fulfilled,
		private ?OrderReference $cancelled = null,
		private ?string $deliveryTrackingNumber = null,
	)
	{
		if ($fulfilled->getItems() === []) {
			throw new InvalidArgumentException('Fulfilled has no items.');
		}
		if ($cancelled !== null && $cancelled->getItems() === []) {
			throw new InvalidArgumentException('Cancelled has no items.');
		}
	}

	public function send(ApiClient $apiClient): StatusDetailPaymentResponse
	{
		$requestData = array_filter([
			'merchantId' => $this->merchantId,
			'payId' => $this->payId,
			'event' => $this->event->value,
			'date' => $this->date->format('Ymd'),
			'fulfilled' => $this->fulfilled->encode(),
			'cancelled' => $this->cancelled?->encode(),
			'deliveryTrackingNumber' => $this->deliveryTrackingNumber,
		], EncodeHelper::filterValueCallback());

		$response = $apiClient->put(
			'mallpay/logistics',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'payId' => null,
				'event' => null,
				'date' => null,
				'fulfilled' => OrderReference::encodeForSignature(),
				'cancelled' => OrderReference::encodeForSignature(),
				'deliveryTrackingNumber' => null,
				'dttm' => null,
			]),
			new SignatureDataFormatter(StatusDetailPaymentResponse::encodeForSignature()),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return StatusDetailPaymentResponse::createFromResponseData($data);
	}

}
