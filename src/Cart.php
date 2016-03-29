<?php

namespace SlevomatCsobGateway;

class Cart
{

	/**
	 * @var CartItem[]
	 */
	private $items = [];

	/**
	 * @var Currency
	 */
	private $currency;

	public function __construct(Currency $currency)
	{
		$this->currency = $currency;
	}

	/**
	 * @param string $name
	 * @param int $quantity
	 * @param int $amount
	 * @param string|null $description
	 */
	public function addItem($name, $quantity, $amount, $description = null)
	{
		$this->items[] = new CartItem($name, $quantity, $amount, $description);
	}

	/**
	 * @return CartItem[]
	 */
	public function getItems()
	{
		return $this->items;
	}

	/**
	 * @return Currency
	 */
	public function getCurrency()
	{
		return $this->currency;
	}

	/**
	 * @return int
	 */
	public function countTotalAmount()
	{
		$totalAmount = 0;

		foreach ($this->items as $item) {
			$totalAmount += $item->getAmount();
		}

		return $totalAmount;
	}

}
