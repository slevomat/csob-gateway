<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Extension;

use DateTimeImmutable;

class TransactionSettlementResponse
{

	/**
	 * @var DateTimeImmutable
	 */
	private $createdDate;

	/**
	 * @var DateTimeImmutable|null
	 */
	private $authDate;

	/**
	 * @var DateTimeImmutable|null
	 */
	private $settlementDate;

	public function __construct(
		DateTimeImmutable $createdDate,
		?DateTimeImmutable $authDate,
		?DateTimeImmutable $settlementDate
	)
	{
		$this->createdDate = $createdDate;
		$this->authDate = $authDate;
		$this->settlementDate = $settlementDate;
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
