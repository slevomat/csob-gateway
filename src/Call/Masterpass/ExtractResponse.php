<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Masterpass;

use DateTimeImmutable;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;

class ExtractResponse
{

	/**
	 * @param mixed[]|null $checkoutParams
	 */
	public function __construct(
		private string $payId,
		private DateTimeImmutable $responseDateTime,
		private ResultCode $resultCode,
		private string $resultMessage,
		private ?PaymentStatus $paymentStatus = null,
		private ?array $checkoutParams = null,
	)
	{
	}

	public function getPayId(): string
	{
		return $this->payId;
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

	public function getPaymentStatus(): ?PaymentStatus
	{
		return $this->paymentStatus;
	}

	/**
	 * @return mixed[]|null
	 */
	public function getCheckoutParams(): ?array
	{
		return $this->checkoutParams;
	}

}
