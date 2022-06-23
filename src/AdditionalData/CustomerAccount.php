<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\AdditionalData;

use DateTimeImmutable;
use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\Validator;
use function array_filter;
use const DATE_ATOM;

class CustomerAccount
{

	public const QUANTITY_MIN = 0;
	public const QUANTITY_MAX = 9999;

	public function __construct(
		private ?DateTimeImmutable $createdAt = null,
		private ?DateTimeImmutable $changedAt = null,
		private ?DateTimeImmutable $changedPwdAt = null,
		private ?int $orderHistory = null,
		private ?int $paymentsDay = null,
		private ?int $paymentsYear = null,
		private ?int $oneclickAdds = null,
		private ?bool $suspicious = null,
	)
	{
		if ($this->orderHistory !== null) {
			Validator::checkNumberRange($this->orderHistory, self::QUANTITY_MIN, self::QUANTITY_MAX);
		}
		if ($this->paymentsDay !== null) {
			Validator::checkNumberRange($this->paymentsDay, self::QUANTITY_MIN, self::QUANTITY_MAX);
		}
		if ($this->paymentsYear !== null) {
			Validator::checkNumberRange($this->paymentsYear, self::QUANTITY_MIN, self::QUANTITY_MAX);
		}
		if ($this->oneclickAdds !== null) {
			Validator::checkNumberRange($this->oneclickAdds, self::QUANTITY_MIN, self::QUANTITY_MAX);
		}
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'createdAt' => $this->createdAt?->format(DATE_ATOM),
			'changedAt' => $this->changedAt?->format(DATE_ATOM),
			'changedPwdAt' => $this->changedPwdAt?->format(DATE_ATOM),
			'orderHistory' => $this->orderHistory,
			'paymentsDay' => $this->paymentsDay,
			'paymentsYear' => $this->paymentsYear,
			'oneclickAdds' => $this->oneclickAdds,
			'suspicious' => $this->suspicious,
		], EncodeHelper::filterValueCallback());
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'createdAt' => null,
			'changedAt' => null,
			'changedPwdAt' => null,
			'orderHistory' => null,
			'paymentsDay' => null,
			'paymentsYear' => null,
			'oneclickAdds' => null,
			'suspicious' => null,
		];
	}

}
