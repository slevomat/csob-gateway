<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\AdditionalData;

use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\Validator;
use function array_filter;

class FingerprintSdk
{

	public const MAX_TIMEOUT_MIN = 5;
	public const REFERENCE_NUMBER_LENGTH_MAX = 32;
	public const TRANS_ID_LENGTH_MAX = 36;

	public function __construct(
		private string $appID,
		private string $encData,
		private string $ephemPubKey,
		private int $maxTimeout,
		private string $referenceNumber,
		private string $transID,
	)
	{
		Validator::checkNumberGraterEqualThen($this->maxTimeout, self::MAX_TIMEOUT_MIN);
		Validator::checkWhitespacesAndLength($this->referenceNumber, self::REFERENCE_NUMBER_LENGTH_MAX);
		Validator::checkWhitespacesAndLength($this->transID, self::TRANS_ID_LENGTH_MAX);
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'appID' => $this->appID,
			'encData' => $this->encData,
			'ephemPubKey' => $this->ephemPubKey,
			'maxTimeout' => $this->maxTimeout,
			'referenceNumber' => $this->referenceNumber,
			'transID' => $this->transID,
		], EncodeHelper::filterValueCallback());
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'appID' => null,
			'encData' => null,
			'ephemPubKey' => null,
			'maxTimeout' => null,
			'referenceNumber' => null,
			'transID' => null,
		];
	}

}
