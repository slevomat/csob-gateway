<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use SlevomatCsobGateway\Validator;

class Address
{

	public const NAME_LENGTH_MAX = 40;
	public const CITY_LENGTH_MAX = 50;
	public const STREET_ADDRESS_LENGTH_MAX = 100;
	public const STREET_NUMBER_LENGTH_MAX = 25;
	public const ZIP_LENGTH_MAX = 10;

	/** @var string|null */
	private $name;

	/** @var Country */
	private $country;

	/** @var string */
	private $city;

	/** @var string */
	private $streetAddress;

	/** @var string|null */
	private $streetNumber;

	/** @var string */
	private $zip;

	/** @var AddressType */
	private $addressType;

	public function __construct(
		?string $name,
		Country $country,
		string $city,
		string $streetAddress,
		?string $streetNumber,
		string $zip,
		AddressType $addressType
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

		$this->name = $name;
		$this->country = $country;
		$this->city = $city;
		$this->streetAddress = $streetAddress;
		$this->streetNumber = $streetNumber;
		$this->zip = $zip;
		$this->addressType = $addressType;
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		$data = [
			'country' => $this->country->getValue(),
			'city' => $this->city,
			'streetAddress' => $this->streetAddress,
			'zip' => $this->zip,
			'addressType' => $this->addressType->getValue(),
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
