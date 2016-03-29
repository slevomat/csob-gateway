<?php

namespace SlevomatCsobGateway;

class CartItem
{

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var int
	 */
	private $quantity;

	/**
	 * @var int
	 */
	private $amount;

	/**
	 * @var string|null
	 */
	private $description;

	/**
	 * @param string $name
	 * @param int $quantity
	 * @param int $amount
	 * @param string|null $description
	 */
	public function __construct($name, $quantity, $amount, $description = null)
	{
		Validator::checkCartItemName($name);
		if ($description !== null) {
			Validator::checkCartItemDescription($description);
		}
		Validator::checkCartItemQuantity($quantity);

		$this->name = $name;
		$this->quantity = $quantity;
		$this->amount = $amount;
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return int
	 */
	public function getQuantity()
	{
		return $this->quantity;
	}

	/**
	 * @return int
	 */
	public function getAmount()
	{
		return $this->amount;
	}

	/**
	 * @return string|null
	 */
	public function getDescription()
	{
		return $this->description;
	}

}
