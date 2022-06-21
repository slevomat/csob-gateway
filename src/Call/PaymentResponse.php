<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Validator;

class PaymentResponse
{

	/**
	 * @param mixed[] $extensions
	 */
	public function __construct(
		private string $payId,
		private DateTimeImmutable $responseDateTime,
		private ResultCode $resultCode,
		private string $resultMessage,
		private ?PaymentStatus $paymentStatus = null,
		private ?string $authCode = null,
		private ?string $merchantData = null,
		private array $extensions = [],
		private ?string $statusDetail = null,
	)
	{
		Validator::checkPayId($payId);
		if ($merchantData !== null) {
			Validator::checkMerchantData($merchantData);
		}
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
