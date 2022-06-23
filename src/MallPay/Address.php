<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use SlevomatCsobGateway\Country;
use SlevomatCsobGateway\Validator;

class Address
{

	public const NAME_LENGTH_MAX = 40;
	public const CITY_LENGTH_MAX = 50;
	public const STREET_ADDRESS_LENGTH_MAX = 100;
	public const STREET_NUMBER_LENGTH_MAX = 25;
	public const ZIP_LENGTH_MAX = 10;

	public function __construct(
		private ?string $name,
		private Country $country,
		private string $city,
		private string $streetAddress,
		private ?string $streetNumber,
		private string $zip,
		private AddressType $addressType,
	)
	{
		Validator::checkWhitespacesAndLength($city, self::CITY_LENGTH_MAX);
		Validator::checkWhitespacesAndLength($streetAddress, self::STREET_ADDRESS_LENGTH_MAX);
		Validator::checkWhitespacesAndLength($zip, self::ZIP_LENGTH_MAX);
		if ($name !== null) {
			Validator::checkWhitespacesAndLength($name, self::NAME_LENGTH_MAX);
		}
		if ($streetNumber !== null) {
			Validator::checkWhitespacesAndLength($streetNumber, self::STREET_NUMBER_LENGTH_MAX);
		}
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		$data = [
			'country' => $this->country->value,
			'city' => $this->city,
			'streetAddress' => $this->streetAddress,
			'zip' => $this->zip,
			'addressType' => $this->addressType->value,
		];

		if ($this->name !== null) {
			$data['name'] = $this->name;
		}
		if ($this->streetNumber !== null) {
			$data['streetNumber'] = $this->streetNumber;
		}

		return $data;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function getCountry(): Country
	{
		return $this->country;
	}

	public function getCity(): string
	{
		return $this->city;
	}

	public function getStreetAddress(): string
	{
		return $this->streetAddress;
	}

	public function getStreetNumber(): ?string
	{
		return $this->streetNumber;
	}

	public function getZip(): string
	{
		return $this->zip;
	}

	public function getAddressType(): AddressType
	{
		return $this->addressType;
	}

}
