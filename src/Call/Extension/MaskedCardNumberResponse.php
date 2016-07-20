<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Extension;

use DateTimeImmutable;

class MaskedCardNumberResponse
{

	/**
	 * @var string
	 */
	private $longMaskedCln;

	/**
	 * @var string
	 */
	private $maskedCln;

	/**
	 * @var DateTimeImmutable
	 */
	private $expiration;

	public function __construct(
		string $longMaskedCln,
		string $maskedCln,
		DateTimeImmutable $expiration
	)
	{
		$this->longMaskedCln = $longMaskedCln;
		$this->maskedCln = $maskedCln;
		$this->expiration = $expiration;
	}

	public function getLongMaskedCln(): string
	{
		return $this->longMaskedCln;
	}

	public function getMaskedCln(): string
	{
		return $this->maskedCln;
	}

	public function getExpiration(): DateTimeImmutable
	{
		return $this->expiration;
	}

}
