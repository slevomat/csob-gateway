<?php

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;

class EchoResponse
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
	 * @param DateTimeImmutable $responseDateTime
	 * @param ResultCode $resultCode
	 * @param string $resultMessage
	 */
	public function __construct(
		DateTimeImmutable $responseDateTime,
		ResultCode $resultCode,
		$resultMessage
	)
	{
		$this->responseDateTime = $responseDateTime;
		$this->resultCode = $resultCode;
		$this->resultMessage = $resultMessage;
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

}
