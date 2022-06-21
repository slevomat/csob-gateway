<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\MallPay;

use DateTimeImmutable;
use SlevomatCsobGateway\Call\PaymentResponse;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;

class InitMallPayResponse extends PaymentResponse
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
		array $extensions = [],
		private ?string $mallpayUrl = null,
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
			$extensions,
		);
	}

	public function getMallpayUrl(): ?string
	{
		return $this->mallpayUrl;
	}

}
