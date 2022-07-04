<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\AdditionalData;

use SlevomatCsobGateway\Country;
use SlevomatCsobGateway\Encodable;
use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\Validator;
use function array_filter;

class OrderAddress implements Encodable
{

	public const ADDRESS_LENGTH_MAX = 50;
	public const ZIP_LENGTH_MAX = 50;

	public function __construct(
		private string $address1,
		private ?string $address2,
		private ?string $address3,
		private string $city,
		private string $zip,
		private ?string $state,
		private Country $country,
	)
	{
		Validator::checkWhitespacesAndLength($this->address1, self::ADDRESS_LENGTH_MAX);
		if ($this->address2 !== null) {
			Validator::checkWhitespacesAndLength($this->address2, self::ADDRESS_LENGTH_MAX);
		}
		if ($this->address3 !== null) {
			Validator::checkWhitespacesAndLength($this->address3, self::ADDRESS_LENGTH_MAX);
		}
		if ($this->city !== null) {
			Validator::checkWhitespacesAndLength($this->city, self::ADDRESS_LENGTH_MAX);
		}
		Validator::checkWhitespacesAndLength($this->zip, self::ZIP_LENGTH_MAX);
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'address1' => $this->address1,
			'address2' => $this->address2,
			'address3' => $this->address3,
			'city' => $this->city,
			'zip' => $this->zip,
			'state' => $this->state,
			'country' => $this->country->getLongCode(),
		], EncodeHelper::filterValueCallback());
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'address1' => null,
			'address2' => null,
			'address3' => null,
			'city' => null,
			'zip' => null,
			'state' => null,
			'country' => null,
		];
	}

}
