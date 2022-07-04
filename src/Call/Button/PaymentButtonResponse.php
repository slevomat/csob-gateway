<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Button;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Call\PaymentResponse;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\EncodeHelper;
use function array_filter;
use function array_key_exists;
use function array_merge;

class PaymentButtonResponse extends PaymentResponse
{

	public function __construct(
		string $payId,
		DateTimeImmutable $responseDateTime,
		ResultCode $resultCode,
		string $resultMessage,
		?PaymentStatus $paymentStatus = null,
		private ?PaymentButtonRedirect $redirect = null,
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
			array_key_exists('redirect', $data) ? new PaymentButtonRedirect(
				HttpMethod::from($data['redirect']['method']),
				$data['redirect']['url'],
				$data['redirect']['params'] ?? null,
			) : null,
		);
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return array_merge(parent::encodeForSignature(), [
			'redirect' => PaymentButtonRedirect::encodeForSignature(),
		]);
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter(array_merge(parent::encode(), [
			'redirect' => $this->redirect,
		]), EncodeHelper::filterValueCallback());
	}

	public function getRedirect(): ?PaymentButtonRedirect
	{
		return $this->redirect;
	}

}
