<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\GooglePay;

use SlevomatCsobGateway\Country;
use SlevomatCsobGateway\Encodable;
use SlevomatCsobGateway\EncodeHelper;
use function array_filter;

class InitParams implements Encodable
{

	/**
	 * @param string[] $allowedCardNetworks
	 * @param string[] $allowedCardAuthMethods
	 */
	public function __construct(
		private int $apiVersion,
		private int $apiVersionMinor,
		private string $paymentMethodType,
		private array $allowedCardNetworks,
		private array $allowedCardAuthMethods,
		private bool $assuranceDetailsRequired,
		private bool $billingAddressRequired,
		private string $billingAddressParametersFormat,
		private string $tokenizationSpecificationType,
		private string $gateway,
		private string $gatewayMerchantId,
		private string $googlepayMerchantId,
		private string $merchantName,
		private InitParamsEnvironment $environment,
		private string $totalPriceStatus,
		private Country $countryCode,
	)
	{
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'apiVersion' => null,
			'apiVersionMinor' => null,
			'paymentMethodType' => null,
			'allowedCardNetworks' => [],
			'allowedCardAuthMethods' => [],
			'assuranceDetailsRequired' => null,
			'billingAddressRequired' => null,
			'billingAddressParametersFormat' => null,
			'tokenizationSpecificationType' => null,
			'gateway' => null,
			'gatewayMerchantId' => null,
			'googlepayMerchantId' => null,
			'merchantName' => null,
			'environment' => null,
			'totalPriceStatus' => null,
			'countryCode' => null,
		];
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'apiVersion' => $this->apiVersion,
			'apiVersionMinor' => $this->apiVersionMinor,
			'paymentMethodType' => $this->paymentMethodType,
			'allowedCardNetworks' => $this->allowedCardNetworks,
			'allowedCardAuthMethods' => $this->allowedCardAuthMethods,
			'assuranceDetailsRequired' => $this->assuranceDetailsRequired,
			'billingAddressRequired' => $this->billingAddressRequired,
			'billingAddressParametersFormat' => $this->billingAddressParametersFormat,
			'tokenizationSpecificationType' => $this->tokenizationSpecificationType,
			'gateway' => $this->gateway,
			'gatewayMerchantId' => $this->gatewayMerchantId,
			'googlepayMerchantId' => $this->googlepayMerchantId,
			'merchantName' => $this->merchantName,
			'environment' => $this->environment->value,
			'totalPriceStatus' => $this->totalPriceStatus,
			'countryCode' => $this->countryCode->value,
		], EncodeHelper::filterValueCallback());
	}

	public function getApiVersion(): int
	{
		return $this->apiVersion;
	}

	public function getApiVersionMinor(): int
	{
		return $this->apiVersionMinor;
	}

	public function getPaymentMethodType(): string
	{
		return $this->paymentMethodType;
	}

	/**
	 * @return string[]
	 */
	public function getAllowedCardNetworks(): array
	{
		return $this->allowedCardNetworks;
	}

	/**
	 * @return string[]
	 */
	public function getAllowedCardAuthMethods(): array
	{
		return $this->allowedCardAuthMethods;
	}

	public function isAssuranceDetailsRequired(): bool
	{
		return $this->assuranceDetailsRequired;
	}

	public function isBillingAddressRequired(): bool
	{
		return $this->billingAddressRequired;
	}

	public function getBillingAddressParametersFormat(): string
	{
		return $this->billingAddressParametersFormat;
	}

	public function getTokenizationSpecificationType(): string
	{
		return $this->tokenizationSpecificationType;
	}

	public function getGateway(): string
	{
		return $this->gateway;
	}

	public function getGatewayMerchantId(): string
	{
		return $this->gatewayMerchantId;
	}

	public function getGooglepayMerchantId(): string
	{
		return $this->googlepayMerchantId;
	}

	public function getMerchantName(): string
	{
		return $this->merchantName;
	}

	public function getEnvironment(): InitParamsEnvironment
	{
		return $this->environment;
	}

	public function getTotalPriceStatus(): string
	{
		return $this->totalPriceStatus;
	}

	public function getCountryCode(): Country
	{
		return $this->countryCode;
	}

}
