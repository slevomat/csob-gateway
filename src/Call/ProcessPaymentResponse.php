<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

class ProcessPaymentResponse
{

	/**
	 * @var string
	 */
	private $gatewayLocationUrl;

	public function __construct(string $gatewayLocationUrl)
	{
		$this->gatewayLocationUrl = $gatewayLocationUrl;
	}

	public function getGatewayLocationUrl(): string
	{
		return $this->gatewayLocationUrl;
	}

}
