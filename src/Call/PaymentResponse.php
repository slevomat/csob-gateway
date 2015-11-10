<?php

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;

class PaymentResponse
{

	/**
	 * @var string
	 */
	private $payId;

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
	 * @var PaymentStatus|null
	 */
	private $paymentStatus;

	/**
	 * @var string|null
	 */
	private $authCode;

	/**
	 * @param string $payId
	 * @param DateTimeImmutable $responseDateTime
	 * @param ResultCode $resultCode
	 * @param string $resultMessage
	 * @param PaymentStatus|null $paymentStatus
	 * @param string|null $authCode
	 */
	public function __construct(
		$payId,
		DateTimeImmutable $responseDateTime,
		ResultCode $resultCode,
		$resultMessage,
		PaymentStatus $paymentStatus = null,
		$authCode = null
	)
	{
		$this->payId = $payId;
		$this->responseDateTime = $responseDateTime;
		$this->resultCode = $resultCode;
		$this->resultMessage = $resultMessage;
		$this->paymentStatus = $paymentStatus;
		$this->authCode = $authCode;
	}

	/**
	 * @return string
	 */
	public function getPayId()
	{
		return $this->payId;
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
	 * @return PaymentStatus|null
	 */
	public function getPaymentStatus()
	{
		return $this->paymentStatus;
	}

	/**
	 * @return string|null
	 */
	public function getAuthCode()
	{
		return $this->authCode;
	}

}
