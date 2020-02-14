<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;

class InitPaymentResponse extends PaymentResponse
{

	/** @var string|null */
	private $customerCode;

	/**
	 * @param string $payId
	 * @param DateTimeImmutable $responseDateTime
	 * @param ResultCode $resultCode
	 * @param string $resultMessage
	 * @param PaymentStatus|null $paymentStatus
	 * @param string|null $authCode
	 * @param string|null $merchantData
	 * @param string|null $customerCode
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
		?string $customerCode = null,
		array $extensions = []
	)
	{
		parent::__construct($payId, $responseDateTime, $resultCode, $resultMessage, $paymentStatus, $authCode, $merchantData, $extensions);

		$this->customerCode = $customerCode;
	}

	public function getCustomerCode(): ?string
	{
		return $this->customerCode;
	}

}
