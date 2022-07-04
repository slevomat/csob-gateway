<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Encodable;
use SlevomatCsobGateway\EncodeHelper;
use function array_filter;

class ActionsAuthenticateSdkChallenge implements Encodable
{

	public function __construct(
		private string $threeDSServerTransID,
		private string $acsReferenceNumber,
		private string $acsTransID,
		private string $acsSignedContent,
	)
	{
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'threeDSServerTransID' => null,
			'acsReferenceNumber' => null,
			'acsTransID' => null,
			'acsSignedContent' => null,
		];
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'threeDSServerTransID' => $this->threeDSServerTransID,
			'acsReferenceNumber' => $this->acsReferenceNumber,
			'acsTransID' => $this->acsTransID,
			'acsSignedContent' => $this->acsSignedContent,
		], EncodeHelper::filterValueCallback());
	}

	public function getThreeDSServerTransID(): string
	{
		return $this->threeDSServerTransID;
	}

	public function getAcsReferenceNumber(): string
	{
		return $this->acsReferenceNumber;
	}

	public function getAcsTransID(): string
	{
		return $this->acsTransID;
	}

	public function getAcsSignedContent(): string
	{
		return $this->acsSignedContent;
	}

}
