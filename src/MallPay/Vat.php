<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use SlevomatCsobGateway\Currency;
use SlevomatCsobGateway\Encodable;

class Vat implements Encodable
{

	public function __construct(private int $amount, private Currency $currency, private int $vatRate)
	{
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return [
			'amount' => $this->amount,
			'currency' => $this->currency->value,
			'vatRate' => $this->vatRate,
		];
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'amount' => null,
			'currency' => null,
			'vatRate' => null,
		];
	}

	public function getAmount(): int
	{
		return $this->amount;
	}

	public function getCurrency(): Currency
	{
		return $this->currency;
	}

	public function getVatRate(): int
	{
		return $this->vatRate;
	}

}
