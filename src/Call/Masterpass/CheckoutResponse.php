<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Masterpass;

use DateTimeImmutable;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Validator;

class CheckoutResponse
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

	/** @var mixed[]|null */
	private $lightboxParams;

	/**
	 * @param string $payId
	 * @param \DateTimeImmutable $responseDateTime
	 * @param \SlevomatCsobGateway\Call\ResultCode $resultCode
	 * @param string $resultMessage
	 * @param \SlevomatCsobGateway\Call\PaymentStatus|null $paymentStatus
	 * @param mixed[]|null $lightboxParams
	 */
	public function __construct(
		string $payId,
		DateTimeImmutable $responseDateTime,
		ResultCode $resultCode,
		string $resultMessage,
		?PaymentStatus $paymentStatus,
		?array $lightboxParams
	)
	{
		Validator::checkPayId($payId);

		$this->payId = $payId;
		$this->responseDateTime = $responseDateTime;
		$this->resultCode = $resultCode;
		$this->resultMessage = $resultMessage;
		$this->paymentStatus = $paymentStatus;
		$this->lightboxParams = $lightboxParams;
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
	public function getLightboxParams(): ?array
	{
		return $this->lightboxParams;
	}

}
