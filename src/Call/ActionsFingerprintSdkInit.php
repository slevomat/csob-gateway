<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Encodable;
use SlevomatCsobGateway\EncodeHelper;
use function array_filter;

class ActionsFingerprintSdkInit implements Encodable
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

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'directoryServerID' => $this->directoryServerID,
			'schemeId' => $this->schemeId,
			'messageVersion' => $this->messageVersion,
		], EncodeHelper::filterValueCallback());
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
