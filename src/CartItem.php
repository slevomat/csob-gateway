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
	 * @var float
	 */
	private $amount;

	/**
	 * @var string|null
	 */
	private $description;

	/**
	 * @param string $name
	 * @param int $quantity
	 * @param float $amount
	 * @param string|null $description
	 */
	public function __construct($name, $quantity, $amount, $description = null)
	{
		$name = trim($name);

		Validator::checkCardItemName($name);
		if ($description !== null) {
			$description = trim($description);
			Validator::checkCardItemDescription($description);
		}
		Validator::checkCardItemQuantity($quantity);

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
	 * @return float
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
