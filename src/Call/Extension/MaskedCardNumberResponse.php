<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Extension;

use DateTimeImmutable;

class MaskedCardNumberResponse
{

	public function __construct(
		private string $longMaskedCln,
		private string $maskedCln,
		private DateTimeImmutable $expiration,
	)
	{
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
