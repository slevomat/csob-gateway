<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Validator;

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

	public function __construct(
		DateTimeImmutable $responseDateTime,
		ResultCode $resultCode,
		string $resultMessage,
		string $customerId = null
	)
	{
		Validator::checkCustomerId($customerId);

		$this->responseDateTime = $responseDateTime;
		$this->resultCode = $resultCode;
		$this->resultMessage = $resultMessage;
		$this->customerId = $customerId;
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
	 * @return string|null
	 */
	public function getCustomerId()
	{
		return $this->customerId;
	}

}
