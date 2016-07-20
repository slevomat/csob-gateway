<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

class Price
{

	/**
	 * @var int
	 */
	private $amount;

	/**
	 * @var Currency
	 */
	private $currency;

	public function __construct(
		int $amount,
		Currency $currency
	)
	{
		$this->amount = $amount;
		$this->currency = $currency;
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
