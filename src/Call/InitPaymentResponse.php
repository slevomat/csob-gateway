<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\EncodeHelper;
use function array_filter;
use function array_merge;

class InitPaymentResponse extends PaymentResponse
{

	public function __construct(
		string $payId,
		DateTimeImmutable $responseDateTime,
		ResultCode $resultCode,
		string $resultMessage,
		?PaymentStatus $paymentStatus,
		private ?string $customerCode,
		private ?string $statusDetail,
	)
	{
		parent::__construct($payId, $responseDateTime, $resultCode, $resultMessage, $paymentStatus);
	}

	/**
	 * @param mixed[] $data
	 */
	public static function createFromResponseData(array $data): self
	{
		$paymentResponse = parent::createFromResponseData($data);

		return new self(
			$paymentResponse->getPayId(),
			$paymentResponse->getResponseDateTime(),
			$paymentResponse->getResultCode(),
			$paymentResponse->getResultMessage(),
			$paymentResponse->getPaymentStatus(),
			$data['customerCode'] ?? null,
			$data['statusDetail'] ?? null,
		);
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return array_merge(parent::encodeForSignature(), [
			'customerCode' => null,
			'statusDetail' => null,
		]);
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter(array_merge(parent::encode(), [
			'customerCode' => $this->customerCode,
			'statusDetail' => $this->statusDetail,
		]), EncodeHelper::filterValueCallback());
	}

	public function getCustomerCode(): ?string
	{
		return $this->customerCode;
	}

	public function getStatusDetail(): ?string
	{
		return $this->statusDetail;
	}

}
