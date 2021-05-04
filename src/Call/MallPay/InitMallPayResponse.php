<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\MallPay;

use DateTimeImmutable;
use SlevomatCsobGateway\Call\PaymentResponse;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;

class InitMallPayResponse extends PaymentResponse
{

	/** @var string|null */
	private $mallpayUrl;

	/**
	 * @param string $payId
	 * @param DateTimeImmutable $responseDateTime
	 * @param ResultCode $resultCode
	 * @param string $resultMessage
	 * @param PaymentStatus|null $paymentStatus
	 * @param string|null $authCode
	 * @param string|null $merchantData
	 * @param mixed[] $extensions
	 * @param string|null $mallpayUrl
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
		?string $mallpayUrl = null
	)
	{
		parent::__construct(
			$payId,
			$responseDateTime,
			$resultCode,
			$resultMessage,
			$paymentStatus,
			$authCode,
			$merchantData,
			$extensions
		);

		$this->mallpayUrl = $mallpayUrl;
	}

	public function getMallpayUrl(): ?string
	{
		return $this->mallpayUrl;
	}

}
