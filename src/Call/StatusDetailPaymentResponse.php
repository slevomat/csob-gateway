<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\EncodeHelper;
use function array_filter;
use function array_merge;

class StatusDetailPaymentResponse extends PaymentResponse
{

	public function __construct(
		string $payId,
		DateTimeImmutable $responseDateTime,
		ResultCode $resultCode,
		string $resultMessage,
		?PaymentStatus $paymentStatus = null,
		private ?string $statusDetail = null,
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
			$data['statusDetail'] ?? null,
		);
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return array_merge(parent::encodeForSignature(), [
			'statusDetail' => null,
		]);
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter(array_merge(parent::encode(), [
			'statusDetail' => $this->statusDetail,
		]), EncodeHelper::filterValueCallback());
	}

	public function getStatusDetail(): ?string
	{
		return $this->statusDetail;
	}

}
