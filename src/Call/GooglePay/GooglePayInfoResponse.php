<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\GooglePay;

use DateTimeImmutable;
use SlevomatCsobGateway\Call\ResultCode;

class GooglePayInfoResponse
{

	/**
	 * @param mixed[] $checkoutParams
	 */
	public function __construct(private DateTimeImmutable $responseDateTime, private ResultCode $resultCode, private string $resultMessage, private array $checkoutParams)
	{
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
