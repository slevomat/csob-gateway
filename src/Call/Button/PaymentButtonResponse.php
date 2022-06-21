<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Button;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Validator;

class PaymentButtonResponse
{

	/**
	 * @param mixed[]|null $redirectParams
	 */
	public function __construct(
		private string $payId,
		private DateTimeImmutable $responseDateTime,
		private ResultCode $resultCode,
		private string $resultMessage,
		private ?PaymentStatus $paymentStatus = null,
		private ?HttpMethod $redirectMethod = null,
		private ?string $redirectUrl = null,
		private ?array $redirectParams = null,
	)
	{
		Validator::checkPayId($payId);
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

	public function getRedirectMethod(): ?HttpMethod
	{
		return $this->redirectMethod;
	}

	public function getRedirectUrl(): ?string
	{
		return $this->redirectUrl;
	}

	/**
	 * @return mixed[]|null
	 */
	public function getRedirectParams(): ?array
	{
		return $this->redirectParams;
	}

}
