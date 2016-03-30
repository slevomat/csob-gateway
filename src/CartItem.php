<?php declare(strict_types = 1);

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

	public function __construct(string $name, int $quantity, int $amount, string $description = null)
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

	public function getName(): string
	{
		return $this->name;
	}

	public function getQuantity(): int
	{
		return $this->quantity;
	}

	public function getAmount(): int
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
