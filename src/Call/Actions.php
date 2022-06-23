<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

class Actions
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

	public function getFingerprint(): ?ActionsFingerprint
	{
		return $this->fingerprint;
	}

	public function getAuthenticate(): ?ActionsAuthenticate
	{
		return $this->authenticate;
	}

}
