<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

use function array_filter;

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

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'name' => $this->name,
			'quantity' => $this->quantity,
			'amount' => $this->amount,
			'description' => $this->description,
		], EncodeHelper::filterValueCallback());
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'name' => null,
			'quantity' => null,
			'amount' => null,
			'description' => null,
		];
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
