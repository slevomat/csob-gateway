<?php

namespace SlevomatCsobGateway\Call;

class ProcessPaymentResponse
{

	/**
	 * @var string
	 */
	private $gatewayLocationUrl;

	/**
	 * @param string $gatewayLocationUrl
	 */
	public function __construct($gatewayLocationUrl)
	{
		$this->gatewayLocationUrl = (string) $gatewayLocationUrl;
	}

	/**
	 * @return string
	 */
	public function getGatewayLocationUrl()
	{
		return $this->gatewayLocationUrl;
	}

}
