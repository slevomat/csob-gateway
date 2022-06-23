<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

class ProcessPaymentResponse
{

	public function __construct(private string $gatewayLocationUrl)
	{
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
			'merchantData' => null,
			'statusDetail' => null,
		];
	}

	public function getGatewayLocationUrl(): string
	{
		return $this->gatewayLocationUrl;
	}

}
