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
		DateTimeImmutable $authDate = null,
		DateTimeImmutable $settlementDate = null
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

	/**
	 * @return DateTimeImmutable|null
	 */
	public function getAuthDate()
	{
		return $this->authDate;
	}

	/**
	 * @return DateTimeImmutable|null
	 */
	public function getSettlementDate()
	{
		return $this->settlementDate;
	}

}
