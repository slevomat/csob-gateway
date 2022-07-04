<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\GooglePay;

use DateTimeImmutable;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Country;
use SlevomatCsobGateway\EncodeHelper;
use function array_filter;
use function array_key_exists;

class EchoGooglePayResponse
{

	public function __construct(
		private DateTimeImmutable $responseDateTime,
		private ResultCode $resultCode,
		private string $resultMessage,
		private ?InitParams $initParams = null,
	)
	{
	}

	/**
	 * @param mixed[] $data
	 */
	public static function createFromResponseData(array $data): self
	{
		return new self(
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			ResultCode::from($data['resultCode']),
			$data['resultMessage'],
			array_key_exists('initParams', $data) ? new InitParams(
				$data['initParams']['apiVersion'],
				$data['initParams']['apiVersionMinor'],
				$data['initParams']['paymentMethodType'],
				$data['initParams']['allowedCardNetworks'],
				$data['initParams']['allowedCardAuthMethods'],
				$data['initParams']['assuranceDetailsRequired'],
				$data['initParams']['billingAddressRequired'],
				$data['initParams']['billingAddressParametersFormat'],
				$data['initParams']['tokenizationSpecificationType'],
				$data['initParams']['gateway'],
				$data['initParams']['gatewayMerchantId'],
				$data['initParams']['googlepayMerchantId'],
				$data['initParams']['merchantName'],
				InitParamsEnvironment::from($data['initParams']['environment']),
				$data['initParams']['totalPriceStatus'],
				Country::from($data['initParams']['countryCode']),
			) : null,
		);
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'dttm' => null,
			'resultCode' => null,
			'resultMessage' => null,
			'initParams' => InitParams::encodeForSignature(),
		];
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'dttm' => $this->responseDateTime->format('YmdHis'),
			'resultCode' => $this->resultCode->value,
			'resultMessage' => $this->resultMessage,
			'initParams' => $this->initParams?->encode(),
		], EncodeHelper::filterValueCallback());
	}

	public function getResponseDateTime(): DateTimeImmutable
	{
		return $this->responseDateTime;
	}

	public function getResultCode(): ResultCode
	{
		return $this->resultCode;
	}

	public function getResultMessage(): string
	{
		return $this->resultMessage;
	}

	public function getInitParams(): ?InitParams
	{
		return $this->initParams;
	}

}
