<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Validator;

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
	 * @var string|null
	 */
	private $merchantData;

	/** @var mixed[] */
	private $extensions;

	/**
	 * @param string $payId
	 * @param \DateTimeImmutable $responseDateTime
	 * @param \SlevomatCsobGateway\Call\ResultCode $resultCode
	 * @param string $resultMessage
	 * @param \SlevomatCsobGateway\Call\PaymentStatus|null $paymentStatus
	 * @param string|null $authCode
	 * @param string|null $merchantData
	 * @param mixed[] $extensions
	 */
	public function __construct(
		string $payId,
		DateTimeImmutable $responseDateTime,
		ResultCode $resultCode,
		string $resultMessage,
		PaymentStatus $paymentStatus = null,
		string $authCode = null,
		string $merchantData = null,
		array $extensions = []
	)
	{
		Validator::checkPayId($payId);
		if ($merchantData !== null) {
			Validator::checkMerchantData($merchantData);
		}

		$this->payId = $payId;
		$this->responseDateTime = $responseDateTime;
		$this->resultCode = $resultCode;
		$this->resultMessage = $resultMessage;
		$this->paymentStatus = $paymentStatus;
		$this->authCode = $authCode;
		$this->merchantData = $merchantData;
		$this->extensions = $extensions;
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

	/**
	 * @return null|string
	 */
	public function getMerchantData()
	{
		return $this->merchantData;
	}

	/**
	 * @return mixed[]
	 */
	public function getExtensions(): array
	{
		return $this->extensions;
	}

}
