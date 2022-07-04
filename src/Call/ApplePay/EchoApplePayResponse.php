<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\ApplePay;

use DateTimeImmutable;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Country;
use SlevomatCsobGateway\EncodeHelper;
use function array_filter;
use function array_key_exists;

class EchoApplePayResponse
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
				Country::from($data['initParams']['countryCode']),
				$data['initParams']['supportedNetworks'],
				$data['initParams']['merchantCapabilities'],
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
