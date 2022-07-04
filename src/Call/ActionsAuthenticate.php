<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Encodable;
use SlevomatCsobGateway\EncodeHelper;
use function array_filter;

class ActionsAuthenticate implements Encodable
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

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'browserChallenge' => $this->browserChallenge?->encode(),
			'sdkChallenge' => $this->sdkChallenge?->encode(),
		], EncodeHelper::filterValueCallback());
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
