<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

class Price
{

	public function __construct(private int $amount, private Currency $currency)
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

}
