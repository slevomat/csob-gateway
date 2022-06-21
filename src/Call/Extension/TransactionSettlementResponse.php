<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Extension;

use DateTimeImmutable;

class TransactionSettlementResponse
{

	public function __construct(
		private DateTimeImmutable $createdDate,
		private ?DateTimeImmutable $authDate = null,
		private ?DateTimeImmutable $settlementDate = null,
	)
	{
	}

	public function getCreatedDate(): DateTimeImmutable
	{
		return $this->createdDate;
	}

	public function getAuthDate(): ?DateTimeImmutable
	{
		return $this->authDate;
	}

	public function getSettlementDate(): ?DateTimeImmutable
	{
		return $this->settlementDate;
	}

}
