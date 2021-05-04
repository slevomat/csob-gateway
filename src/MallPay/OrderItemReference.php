<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use SlevomatCsobGateway\Validator;

class OrderItemReference
{

	/** @var string */
	private $code;

	/** @var string|null */
	private $ean;

	/** @var string */
	private $name;

	/** @var OrderItemType|null */
	private $type;

	/** @var int|null */
	private $quantity;

	public function __construct(
		string $code,
		?string $ean,
		string $name,
		?OrderItemType $type,
		?int $quantity
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

		$this->code = $code;
		$this->ean = $ean;
		$this->name = $name;
		$this->type = $type;
		$this->quantity = $quantity;
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
