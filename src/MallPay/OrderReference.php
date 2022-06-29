<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\Price;
use function array_filter;
use function array_map;

class OrderReference
{

	/** @var OrderItemReference[] */
	private array $items = [];

	/**
	 * @param Vat[] $totalVat
	 */
	public function __construct(private Price $totalPrice, private array $totalVat)
	{
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
		return array_filter([
			'totalPrice' => $this->totalPrice->encode(),
			'totalVat' => array_map(static fn (Vat $vat): array => $vat->encode(), $this->totalVat),
			'items' => array_map(static fn (OrderItemReference $item): array => $item->encode(), $this->items),
		], EncodeHelper::filterValueCallback());
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'totalPrice' => Price::encodeForSignature(),
			'totalVat' => [
				Vat::encodeForSignature(),
			],
			'items' => [
				OrderItemReference::encodeForSignature(),
			],
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
