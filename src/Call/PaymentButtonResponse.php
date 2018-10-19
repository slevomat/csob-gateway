<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Validator;

class PaymentButtonResponse
{

	/** @var string */
	private $payId;

	/** @var DateTimeImmutable */
	private $responseDateTime;

	/** @var ResultCode */
	private $resultCode;

	/** @var string */
	private $resultMessage;

	/** @var PaymentStatus|null */
	private $paymentStatus;

	/** @var \SlevomatCsobGateway\Api\HttpMethod|null */
	private $redirectMethod;

	/** @var string|null */
	private $redirectUrl;

	/** @var mixed[]|null */
	private $redirectParams;

	/**
	 * @param string $payId
	 * @param \DateTimeImmutable $responseDateTime
	 * @param \SlevomatCsobGateway\Call\ResultCode $resultCode
	 * @param string $resultMessage
	 * @param \SlevomatCsobGateway\Call\PaymentStatus|null $paymentStatus
	 * @param \SlevomatCsobGateway\Api\HttpMethod|null $redirectMethod
	 * @param string|null $redirectUrl
	 * @param mixed[]|null $redirectParams
	 */
	public function __construct(
		string $payId,
		DateTimeImmutable $responseDateTime,
		ResultCode $resultCode,
		string $resultMessage,
		?PaymentStatus $paymentStatus,
		?HttpMethod $redirectMethod,
		?string $redirectUrl,
		?array $redirectParams
	)
	{
		Validator::checkPayId($payId);

		$this->payId = $payId;
		$this->responseDateTime = $responseDateTime;
		$this->resultCode = $resultCode;
		$this->resultMessage = $resultMessage;
		$this->paymentStatus = $paymentStatus;
		$this->redirectMethod = $redirectMethod;
		$this->redirectUrl = $redirectUrl;
		$this->redirectParams = $redirectParams;
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
