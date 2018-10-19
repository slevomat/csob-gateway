<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

class Cart
{

	/** @var CartItem[] */
	private $items = [];

	/** @var Currency */
	private $currency;

	public function __construct(Currency $currency)
	{
		$this->currency = $currency;
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
			$this->currency
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
