<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

class ProcessPaymentResponse implements Response
{

	public function __construct(private string $gatewayLocationUrl)
	{
	}

	/**
	 * @param mixed[] $data
	 */
	public static function createFromResponseData(array $data): self
	{
		return new self($data['gatewayLocationUrl']);
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

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return [
			'gatewayLocationUrl' => $this->gatewayLocationUrl,
		];
	}

	public function getGatewayLocationUrl(): string
	{
		return $this->gatewayLocationUrl;
	}

}
