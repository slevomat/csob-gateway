<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Encodable;
use SlevomatCsobGateway\EncodeHelper;
use function array_filter;

class Actions implements Encodable
{

	public function __construct(
		private ?ActionsFingerprint $fingerprint = null,
		private ?ActionsAuthenticate $authenticate = null,
	)
	{
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'fingerprint' => ActionsFingerprint::encodeForSignature(),
			'authenticate' => ActionsAuthenticate::encodeForSignature(),
		];
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'fingerprint' => $this->fingerprint?->encode(),
			'authenticate' => $this->authenticate?->encode(),
		], EncodeHelper::filterValueCallback());
	}

	public function getFingerprint(): ?ActionsFingerprint
	{
		return $this->fingerprint;
	}

	public function getAuthenticate(): ?ActionsAuthenticate
	{
		return $this->authenticate;
	}

}
