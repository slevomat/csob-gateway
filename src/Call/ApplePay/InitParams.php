<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\ApplePay;

use SlevomatCsobGateway\Country;
use SlevomatCsobGateway\Encodable;
use SlevomatCsobGateway\EncodeHelper;
use function array_filter;

class InitParams implements Encodable
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

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'countryCode' => $this->countryCode->value,
			'supportedNetworks' => $this->supportedNetworks,
			'merchantCapabilities' => $this->merchantCapabilities,
		], EncodeHelper::filterValueCallback());
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
