<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

class ActionsFingerprint
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

	public function getBrowserInit(): ?ActionsEndpoint
	{
		return $this->browserInit;
	}

	public function getSdkInit(): ?ActionsFingerprintSdkInit
	{
		return $this->sdkInit;
	}

}
