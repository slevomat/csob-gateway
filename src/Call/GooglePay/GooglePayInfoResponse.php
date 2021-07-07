<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\GooglePay;

use DateTimeImmutable;
use SlevomatCsobGateway\Call\ResultCode;

class GooglePayInfoResponse
{

	/** @var DateTimeImmutable */
	private $responseDateTime;

	/** @var ResultCode */
	private $resultCode;

	/** @var string */
	private $resultMessage;

	/** @var mixed[] */
	private $checkoutParams;

	/**
	 * @param DateTimeImmutable $responseDateTime
	 * @param ResultCode $resultCode
	 * @param string $resultMessage
	 * @param mixed[] $checkoutParams
	 */
	public function __construct(DateTimeImmutable $responseDateTime, ResultCode $resultCode, string $resultMessage, array $checkoutParams)
	{
		$this->responseDateTime = $responseDateTime;
		$this->resultCode = $resultCode;
		$this->resultMessage = $resultMessage;
		$this->checkoutParams = $checkoutParams;
	}

	public function getResponseDateTime(): DateTimeImmutable
	{
		return $this->responseDateTime;
	}

	public function getResultCode(): ResultCode
	{
		return $this->resultCode;
	}

	public function getResultMessage(): string
	{
		return $this->resultMessage;
	}

	/**
	 * @return mixed[]
	 */
	public function getCheckoutParams(): array
	{
		return $this->checkoutParams;
	}

}
