<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\AdditionalData;

use SlevomatCsobGateway\Encodable;
use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\Price;
use SlevomatCsobGateway\Validator;
use function array_filter;

class OrderGiftcards implements Encodable
{

	public const QUANTITY_MIN = 0;
	public const QUANTITY_MAX = 99;

	public function __construct(
		private ?Price $totalPrice = null,
		private ?int $quantity = null,
	)
	{
		if ($this->quantity !== null) {
			Validator::checkNumberRange($this->quantity, self::QUANTITY_MIN, self::QUANTITY_MAX);
		}
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'totalAmount' => $this->totalPrice?->getAmount(),
			'currency' => $this->totalPrice?->getCurrency()->value,
			'quantity' => $this->quantity,
		], EncodeHelper::filterValueCallback());
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'totalAmount' => null,
			'currency' => null,
			'quantity' => null,
		];
	}

}
