<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\Validator;
use function array_filter;

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
		return array_filter([
			'code' => $this->code,
			'ean' => $this->ean,
			'name' => $this->name,
			'type' => $this->type?->value,
			'quantity' => $this->quantity,
		], EncodeHelper::filterValueCallback());
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'code' => null,
			'ean' => null,
			'name' => null,
			'type' => null,
			'quantity' => null,
		];
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
