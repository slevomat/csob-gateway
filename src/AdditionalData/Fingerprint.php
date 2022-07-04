<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\AdditionalData;

use SlevomatCsobGateway\Encodable;
use SlevomatCsobGateway\EncodeHelper;
use function array_filter;

class Fingerprint implements Encodable
{

	public function __construct(
		private ?FingerprintBrowser $browser = null,
		private ?FingerprintSdk $sdk = null,
	)
	{
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'browser' => $this->browser?->encode(),
			'sdk' => $this->sdk?->encode(),
		], EncodeHelper::filterValueCallback());
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'browser' => FingerprintBrowser::encodeForSignature(),
			'sdk' => FingerprintSdk::encodeForSignature(),
		];
	}

}
