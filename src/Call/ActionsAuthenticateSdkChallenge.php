<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

class ActionsAuthenticateSdkChallenge
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
