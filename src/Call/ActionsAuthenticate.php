<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

class ActionsAuthenticate
{

	public function __construct(
		private ?ActionsEndpoint $browserChallenge = null,
		private ?ActionsAuthenticateSdkChallenge $sdkChallenge = null,
	)
	{
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'browserChallenge' => ActionsEndpoint::encodeForSignature(),
			'sdkChallenge' => ActionsAuthenticateSdkChallenge::encodeForSignature(),
		];
	}

	public function getBrowserChallenge(): ?ActionsEndpoint
	{
		return $this->browserChallenge;
	}

	public function getSdkChallenge(): ?ActionsAuthenticateSdkChallenge
	{
		return $this->sdkChallenge;
	}

}
