<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use function array_key_exists;
use function base64_decode;

class ReceivePaymentResponse extends AuthCodeStatusDetailPaymentResponse
{

	public function __construct(
		string $payId,
		DateTimeImmutable $responseDateTime,
		ResultCode $resultCode,
		string $resultMessage,
		?PaymentStatus $paymentStatus,
		?string $authCode = null,
		private ?string $merchantData = null,
		?string $statusDetail = null,
	)
	{
		parent::__construct($payId, $responseDateTime, $resultCode, $resultMessage, $paymentStatus, $authCode, $statusDetail);
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
			$paymentResponse->getAuthCode(),
			array_key_exists('merchantData', $data) ? (string) base64_decode($data['merchantData'], true) : null,
			$paymentResponse->getStatusDetail(),
		);
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return ProcessPaymentResponse::encodeForSignature();
	}

	public function getMerchantData(): ?string
	{
		return $this->merchantData;
	}

}
