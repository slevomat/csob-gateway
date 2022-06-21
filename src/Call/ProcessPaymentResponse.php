<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

class ProcessPaymentResponse
{

	public function __construct(private string $gatewayLocationUrl)
	{
	}

	public function getGatewayLocationUrl(): string
	{
		return $this->gatewayLocationUrl;
	}

}
