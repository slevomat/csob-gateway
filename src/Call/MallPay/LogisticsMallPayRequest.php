<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\MallPay;

use DateTimeImmutable;
use InvalidArgumentException;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Call\PaymentResponse;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\MallPay\LogisticsEvent;
use SlevomatCsobGateway\MallPay\OrderReference;

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

	public function send(ApiClient $apiClient): PaymentResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'payId' => $this->payId,
			'event' => $this->event->getValue(),
			'date' => $this->date->format('Ymd'),
			'fulfilled' => $this->fulfilled->encode(),
		];

		if ($this->cancelled !== null) {
			$requestData['cancelled'] = $this->cancelled->encode();
		}
		if ($this->deliveryTrackingNumber !== null) {
			$requestData['deliveryTrackingNumber'] = $this->deliveryTrackingNumber;
		}

		$response = $apiClient->put(
			'mallpay/logistics',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'payId' => null,
				'event' => null,
				'date' => null,
				'fulfilled' => [
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
					'items' => [
						[
							'code' => null,
							'ean' => null,
							'name' => null,
							'type' => null,
							'quantity' => null,
						],
					],
				],
				'cancelled' => [
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
					'items' => [
						[
							'code' => null,
							'ean' => null,
							'name' => null,
							'type' => null,
							'quantity' => null,
						],
					],
				],
				'deliveryTrackingNumber' => null,
				'dttm' => null,
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
