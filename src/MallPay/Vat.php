<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use SlevomatCsobGateway\Currency;
use SlevomatCsobGateway\Validator;

class Vat
{

	/** @var int */
	private $amount;

	/** @var Currency */
	private $currency;

	/** @var int */
	private $vatRate;

	public function __construct(
		int $amount,
		Currency $currency,
		int $vatRate
	)
	{
		Validator::checkNumberPositiveOrZero($amount);

		$this->amount = $amount;
		$this->currency = $currency;
		$this->vatRate = $vatRate;
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
