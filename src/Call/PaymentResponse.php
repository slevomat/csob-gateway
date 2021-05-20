<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Validator;

class PaymentResponse
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

	/** @var string|null */
	private $authCode;

	/** @var string|null */
	private $merchantData;

	/** @var mixed[] */
	private $extensions;

	/** @var string|null */
	private $statusDetail;

	/**
	 * @param string $payId
	 * @param DateTimeImmutable $responseDateTime
	 * @param ResultCode $resultCode
	 * @param string $resultMessage
	 * @param PaymentStatus|null $paymentStatus
	 * @param string|null $authCode
	 * @param string|null $merchantData
	 * @param mixed[] $extensions
	 * @param string|null $statusDetail
	 */
	public function __construct(
		string $payId,
		DateTimeImmutable $responseDateTime,
		ResultCode $resultCode,
		string $resultMessage,
		?PaymentStatus $paymentStatus,
		?string $authCode = null,
		?string $merchantData = null,
		array $extensions = [],
		?string $statusDetail = null
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
		$this->statusDetail = $statusDetail;
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

	public function getAuthCode(): ?string
	{
		return $this->authCode;
	}

	public function getMerchantData(): ?string
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

	public function getStatusDetail(): ?string
	{
		return $this->statusDetail;
	}

}
