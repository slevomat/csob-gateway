<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use SlevomatCsobGateway\Currency;
use SlevomatCsobGateway\Validator;

class Vat
{

	public function __construct(private int $amount, private Currency $currency, private int $vatRate)
	{
		Validator::checkNumberPositiveOrZero($amount);
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return [
			'amount' => $this->amount,
			'currency' => $this->currency->getValue(),
			'vatRate' => $this->vatRate,
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
