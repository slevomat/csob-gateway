<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

use function array_map;

class Cart implements Encodable
{

	/** @var CartItem[] */
	private array $items = [];

	public function __construct(private Currency $currency)
	{
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_map(static fn (CartItem $item): array => $item->encode(), $this->items);
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			CartItem::encodeForSignature(),
		];
	}

	public function addItem(string $name, int $quantity, int $amount, ?string $description = null): void
	{
		$this->items[] = new CartItem($name, $quantity, $amount, $description);
	}

	/**
	 * @return CartItem[]
	 */
	public function getItems(): array
	{
		return $this->items;
	}

	public function getCurrentPrice(): Price
	{
		return new Price(
			$this->countTotalAmount(),
			$this->currency,
		);
	}

	private function countTotalAmount(): int
	{
		$totalAmount = 0;

		foreach ($this->items as $item) {
			$totalAmount += $item->getAmount();
		}

		return $totalAmount;
	}

}
