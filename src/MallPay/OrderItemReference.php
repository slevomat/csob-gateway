<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use SlevomatCsobGateway\Validator;

class OrderItemReference
{

	public function __construct(
		private string $code,
		private ?string $ean,
		private string $name,
		private ?OrderItemType $type = null,
		private ?int $quantity = null,
	)
	{
		Validator::checkWhitespacesAndLength($code, OrderItem::CODE_VARIANT_PRODUCER_LENGTH_MAX);
		Validator::checkWhitespacesAndLength($name, OrderItem::NAME_LENGTH_MAX);
		if ($ean !== null) {
			Validator::checkWhitespacesAndLength($ean, OrderItem::EAN_LENGTH_MAX);
		}
		if ($quantity !== null) {
			Validator::checkNumberPositive($quantity);
		}
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		$data = [
			'code' => $this->code,
			'name' => $this->name,
		];

		if ($this->ean !== null) {
			$data['ean'] = $this->ean;
		}
		if ($this->type !== null) {
			$data['type'] = $this->type->getValue();
		}
		if ($this->quantity !== null) {
			$data['quantity'] = $this->quantity;
		}

		return $data;
	}

	public function getCode(): string
	{
		return $this->code;
	}

	public function getEan(): ?string
	{
		return $this->ean;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getType(): ?OrderItemType
	{
		return $this->type;
	}

	public function getQuantity(): ?int
	{
		return $this->quantity;
	}

}
