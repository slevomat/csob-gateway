<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;

class InitPaymentResponse extends PaymentResponse
{

	/**
	 * @param mixed[] $extensions
	 */
	public function __construct(
		string $payId,
		DateTimeImmutable $responseDateTime,
		ResultCode $resultCode,
		string $resultMessage,
		?PaymentStatus $paymentStatus,
		?string $authCode = null,
		?string $merchantData = null,
		private ?string $customerCode = null,
		array $extensions = [],
	)
	{
		parent::__construct($payId, $responseDateTime, $resultCode, $resultMessage, $paymentStatus, $authCode, $merchantData, $extensions);
	}

	public function getCustomerCode(): ?string
	{
		return $this->customerCode;
	}

}
