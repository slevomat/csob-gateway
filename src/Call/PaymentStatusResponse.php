<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;

class PaymentStatusResponse extends ActionsPaymentResponse
{

	/**
	 * @param mixed[] $extensions
	 */
	public function __construct(
		string $payId,
		DateTimeImmutable $responseDateTime,
		ResultCode $resultCode,
		string $resultMessage,
		?PaymentStatus $paymentStatus = null,
		private ?string $authCode = null,
		?string $statusDetail = null,
		?Actions $actions = null,
		private array $extensions = [],
	)
	{
		parent::__construct($payId, $responseDateTime, $resultCode, $resultMessage, $paymentStatus, $statusDetail, $actions);
	}

	/**
	 * @param mixed[] $data
	 */
	public static function createFromResponseData(array $data): self
	{
		$actionsPaymentResponse = parent::createFromResponseData($data);

		return new self(
			$actionsPaymentResponse->getPayId(),
			$actionsPaymentResponse->getResponseDateTime(),
			$actionsPaymentResponse->getResultCode(),
			$actionsPaymentResponse->getResultMessage(),
			$actionsPaymentResponse->getPaymentStatus(),
			$data['authCode'] ?? null,
			$actionsPaymentResponse->getStatusDetail(),
			$actionsPaymentResponse->getActions(),
			$data['extensions'],
		);
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'payId' => null,
			'dttm' => null,
			'resultCode' => null,
			'resultMessage' => null,
			'paymentStatus' => null,
			'authCode' => null,
			'statusDetail' => null,
			'actions' => Actions::encodeForSignature(),
		];
	}

	public function getAuthCode(): ?string
	{
		return $this->authCode;
	}

	/**
	 * @return mixed[]
	 */
	public function getExtensions(): array
	{
		return $this->extensions;
	}

}
