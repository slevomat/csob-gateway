<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Encodable;
use SlevomatCsobGateway\EncodeHelper;
use function array_filter;

class ActionsFingerprint implements Encodable
{

	public function __construct(
		private ?ActionsEndpoint $browserInit = null,
		private ?ActionsFingerprintSdkInit $sdkInit = null,
	)
	{
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'browserInit' => ActionsEndpoint::encodeForSignature(),
			'sdkInit' => ActionsFingerprintSdkInit::encodeForSignature(),
		];
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'browserInit' => $this->browserInit?->encode(),
			'sdkInit' => $this->sdkInit?->encode(),
		], EncodeHelper::filterValueCallback());
	}

	public function getBrowserInit(): ?ActionsEndpoint
	{
		return $this->browserInit;
	}

	public function getSdkInit(): ?ActionsFingerprintSdkInit
	{
		return $this->sdkInit;
	}

}
