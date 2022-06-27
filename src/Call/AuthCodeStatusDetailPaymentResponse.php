<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use function array_merge;

class AuthCodeStatusDetailPaymentResponse extends StatusDetailPaymentResponse
{

	public function __construct(
		string $payId,
		DateTimeImmutable $responseDateTime,
		ResultCode $resultCode,
		string $resultMessage,
		?PaymentStatus $paymentStatus = null,
		private ?string $authCode = null,
		?string $statusDetail = null,
	)
	{
		parent::__construct($payId, $responseDateTime, $resultCode, $resultMessage, $paymentStatus, $statusDetail);
	}

	/**
	 * @param mixed[] $data
	 */
	public static function createFromResponseData(array $data): self
	{
		$statusDetailPaymentResponse = parent::createFromResponseData($data);

		return new self(
			$statusDetailPaymentResponse->getPayId(),
			$statusDetailPaymentResponse->getResponseDateTime(),
			$statusDetailPaymentResponse->getResultCode(),
			$statusDetailPaymentResponse->getResultMessage(),
			$statusDetailPaymentResponse->getPaymentStatus(),
			$data['authCode'] ?? null,
			$statusDetailPaymentResponse->getStatusDetail(),
		);
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		$statusDetailPaymentData = parent::encodeForSignature();
		unset($statusDetailPaymentData['statusDetail']);

		return array_merge($statusDetailPaymentData, [
			'authCode' => null,
			'statusDetail' => null,
		]);
	}

	public function getAuthCode(): ?string
	{
		return $this->authCode;
	}

}
