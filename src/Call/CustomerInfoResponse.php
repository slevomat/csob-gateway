<?php

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;

class CustomerInfoResponse
{

	/**
	 * @var DateTimeImmutable
	 */
	private $responseDateTime;

	/**
	 * @var ResultCode
	 */
	private $resultCode;

	/**
	 * @var string
	 */
	private $resultMessage;

	/**
	 * @var string|null
	 */
	private $customerId;

	/**
	 * @param DateTimeImmutable $responseDateTime
	 * @param ResultCode $resultCode
	 * @param string $resultMessage
	 * @param string|null $customerId
	 */
	public function __construct(
		DateTimeImmutable $responseDateTime,
		ResultCode $resultCode,
		$resultMessage,
		$customerId
	)
	{
		$this->responseDateTime = $responseDateTime;
		$this->resultCode = $resultCode;
		$this->resultMessage = $resultMessage;
		$this->customerId = $customerId;
	}

	/**
	 * @return DateTimeImmutable
	 */
	public function getResponseDateTime()
	{
		return $this->responseDateTime;
	}

	/**
	 * @return ResultCode
	 */
	public function getResultCode()
	{
		return $this->resultCode;
	}

	/**
	 * @return string
	 */
	public function getResultMessage()
	{
		return $this->resultMessage;
	}

	/**
	 * @return string|null
	 */
	public function getCustomerId()
	{
		return $this->customerId;
	}

}
