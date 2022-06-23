<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

class ActionsFingerprintSdkInit
{

	public function __construct(
		private string $directoryServerID,
		private string $schemeId,
		private string $messageVersion,
	)
	{
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'directoryServerID' => null,
			'schemeId' => null,
			'messageVersion' => null,
		];
	}

	public function getDirectoryServerID(): string
	{
		return $this->directoryServerID;
	}

	public function getSchemeId(): string
	{
		return $this->schemeId;
	}

	public function getMessageVersion(): string
	{
		return $this->messageVersion;
	}

}
