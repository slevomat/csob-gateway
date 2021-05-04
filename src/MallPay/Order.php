<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use InvalidArgumentException;
use SlevomatCsobGateway\Currency;
use SlevomatCsobGateway\Price;
use function array_map;

class Order
{

	/** @var Currency */
	private $currency;

	/** @var Address[] */
	private $addresses = [];

	/** @var OrderDeliveryType|null */
	private $deliveryType;

	/** @var OrderCarrierId|null */
	private $carrierId;

	/** @var string|null */
	private $carrierCustom;

	/** @var OrderItem[] */
	private $items = [];

	public function __construct(
		Currency $currency,
		?OrderDeliveryType $deliveryType,
		?OrderCarrierId $carrierId,
		?string $carrierCustom
	)
	{
		$this->currency = $currency;
		$this->deliveryType = $deliveryType;

		if ($deliveryType !== null && $deliveryType->equals(OrderDeliveryType::get(OrderDeliveryType::DELIVERY_CARRIER))) {
			$this->carrierId = $carrierId;
			if ($carrierId === null) {
				if ($carrierCustom === null) {
					throw new InvalidArgumentException('CarrierCustom is null.');
				}
				$this->carrierCustom = $carrierCustom;
			}
		}
	}

	/**
	 * @param string $code
	 * @param string|null $ean
	 * @param string $name
	 * @param OrderItemType|null $type
	 * @param int|null $quantity
	 * @param string|null $variant
	 * @param string|null $description
	 * @param string|null $producer
	 * @param string[]|null $categories
	 * @param int|null $unitAmount
	 * @param int $totalAmount
	 * @param int|null $unitVatAmount
	 * @param int $totalVatAmount
	 * @param int $vatRate
	 * @param string|null $productUrl
	 */
	public function addItem(
		string $code,
		?string $ean,
		string $name,
		?OrderItemType $type,
		?int $quantity,
		?string $variant,
		?string $description,
		?string $producer,
		?array $categories,
		?int $unitAmount,
		int $totalAmount,
		?int $unitVatAmount,
		int $totalVatAmount,
		int $vatRate,
		?string $productUrl
	): void
	{
		$this->items[] = new OrderItem(
			$code,
			$ean,
			$name,
			$type,
			$quantity,
			$variant,
			$description,
			$producer,
			$categories,
			$unitAmount !== null ? new Price($unitAmount, $this->currency) : null,
			$unitVatAmount !== null ? new Vat($unitVatAmount, $this->currency, $vatRate) : null,
			new Price($totalAmount, $this->currency),
			new Vat($totalVatAmount, $this->currency, $vatRate),
			$productUrl
		);
	}

	public function addAddress(
		?string $name,
		Country $country,
		string $city,
		string $streetAddress,
		?string $streetNumber,
		string $zip,
		AddressType $addressType
	): void
	{
		$this->addresses[] = new Address(
			$name,
			$country,
			$city,
			$streetAddress,
			$streetNumber,
			$zip,
			$addressType
		);
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		$data = [
			'totalPrice' => $this->getTotalPrice()->encode(),
			'totalVat' => array_map(static function (Vat $vat): array {
					return $vat->encode();
			}, $this->getTotalVat()),
			'addresses' => array_map(static function (Address $address): array {
					return $address->encode();
			}, $this->addresses),
			'items' => array_map(static function (OrderItem $item): array {
					return $item->encode();
			}, $this->items),
		];

		if ($this->deliveryType !== null) {
			$data['deliveryType'] = $this->deliveryType->getValue();
		}
		if ($this->carrierId !== null) {
			$data['carrierId'] = $this->carrierId->getValue();
		}
		if ($this->carrierCustom !== null) {
			$data['carrierCustom'] = $this->carrierCustom;
		}

		return $data;
	}

	public function getTotalPrice(): Price
	{
		return new Price(
			$this->countTotalPrice(),
			$this->currency
		);
	}

	private function countTotalPrice(): int
	{
		$totalAmount = 0;

		foreach ($this->items as $item) {
			$totalAmount += $item->getTotalPrice()->getAmount();
		}

		return $totalAmount;
	}

	/**
	 * @return Vat[]
	 */
	public function getTotalVat(): array
	{
		$vatRateAmounts = [];
		foreach ($this->items as $orderItem) {
			$vatRate = $orderItem->getTotalVat()->getVatRate();
			$vatRateAmounts[$vatRate] = ($vatRateAmounts[$vatRate] ?? 0) + $orderItem->getTotalVat()->getAmount();
		}

		$totalVatRates = [];
		foreach ($vatRateAmounts as $vatRate => $amount) {
			$totalVatRates[] = new Vat($amount, $this->currency, $vatRate);
		}

		return $totalVatRates;
	}

	/**
	 * @return Address[]
	 */
	public function getAddresses(): array
	{
		return $this->addresses;
	}

	public function getDeliveryType(): ?OrderDeliveryType
	{
		return $this->deliveryType;
	}

	public function getCarrierId(): ?OrderCarrierId
	{
		return $this->carrierId;
	}

	public function getCarrierCustom(): ?string
	{
		return $this->carrierCustom;
	}

	/**
	 * @return OrderItem[]
	 */
	public function getItems(): array
	{
		return $this->items;
	}

}
