<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use SlevomatCsobGateway\Price;
use function array_map;

class OrderReference
{

	/** @var Price */
	private $totalPrice;

	/** @var Vat[] */
	private $totalVat;

	/** @var OrderItemReference[] */
	private $items = [];

	/**
	 * @param Price $totalPrice
	 * @param Vat[] $totalVat
	 */
	public function __construct(Price $totalPrice, array $totalVat)
	{
		$this->totalPrice = $totalPrice;
		$this->totalVat = $totalVat;
	}

	public function addItem(string $code, ?string $ean, string $name, ?OrderItemType $type, ?int $quantity): void
	{
		$this->items[] = new OrderItemReference($code, $ean, $name, $type, $quantity);
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return [
			'totalPrice' => $this->totalPrice->encode(),
			'totalVat' => array_map(static function (Vat $vat): array {
					return $vat->encode();
			}, $this->totalVat),
			'items' => array_map(static function (OrderItemReference $item): array {
					return $item->encode();
			}, $this->items),
		];
	}

	public function getTotalPrice(): Price
	{
		return $this->totalPrice;
	}

	/**
	 * @return Vat[]
	 */
	public function getTotalVat(): array
	{
		return $this->totalVat;
	}

	/**
	 * @return OrderItemReference[]
	 */
	public function getItems(): array
	{
		return $this->items;
	}

}
