<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

class CartItem
{

	public function __construct(private string $name, private int $quantity, private int $amount, private ?string $description = null)
	{
		Validator::checkCartItemName($name);
		if ($description !== null) {
			Validator::checkCartItemDescription($description);
		}
		Validator::checkCartItemQuantity($quantity);
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

	public function getDescription(): ?string
	{
		return $this->description;
	}

}
