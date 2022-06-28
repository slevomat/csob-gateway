<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\ApplePay;

use SlevomatCsobGateway\Country;

class InitParams
{

	/**
	 * @param string[] $supportedNetworks
	 * @param string[] $merchantCapabilities
	 */
	public function __construct(
		private Country $countryCode,
		private array $supportedNetworks,
		private array $merchantCapabilities,
	)
	{
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'countryCode' => null,
			'supportedNetworks' => [],
			'merchantCapabilities' => [],
		];
	}

	public function getCountryCode(): Country
	{
		return $this->countryCode;
	}

	/**
	 * @return string[]
	 */
	public function getSupportedNetworks(): array
	{
		return $this->supportedNetworks;
	}

	/**
	 * @return string[]
	 */
	public function getMerchantCapabilities(): array
	{
		return $this->merchantCapabilities;
	}

}
