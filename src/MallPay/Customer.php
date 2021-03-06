<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use InvalidArgumentException;
use SlevomatCsobGateway\Validator;

class Customer
{

	public const NAME_LENGTH_MAX = 40;
	public const FULL_NAME_LENGTH_MAX = 100;
	public const TITLE_LENGTH_MAX = 20;
	public const EMAIL_LENGTH_MAX = 50;
	public const PHONE_LENGTH_MAX = 16;
	public const TIN_VATIN_LENGTH_MAX = 10;

	/** @var string|null */
	private $firstName;

	/** @var string|null */
	private $lastName;

	/** @var string|null */
	private $fullName;

	/** @var string|null */
	private $titleBefore;

	/** @var string|null */
	private $titleAfter;

	/** @var string */
	private $email;

	/** @var string */
	private $phone;

	/** @var string|null */
	private $tin;

	/** @var string|null */
	private $vatin;

	public function __construct(
		?string $firstName,
		?string $lastName,
		?string $fullName,
		?string $titleBefore,
		?string $titleAfter,
		string $email,
		string $phone,
		?string $tin,
		?string $vatin
	)
	{
		if ($fullName === null && ($firstName === null || $lastName === null)) {
			throw new InvalidArgumentException('Either fullName or (firstName and lastName) must be set.');
		}
		Validator::checkEmail($email);
		Validator::checkWhitespacesAndLength($email, self::EMAIL_LENGTH_MAX);
		Validator::checkWhitespacesAndLength($phone, self::PHONE_LENGTH_MAX);
		if ($firstName !== null) {
			Validator::checkWhitespacesAndLength($firstName, self::NAME_LENGTH_MAX);
		}
		if ($lastName !== null) {
			Validator::checkWhitespacesAndLength($lastName, self::NAME_LENGTH_MAX);
		}
		if ($fullName !== null) {
			Validator::checkWhitespacesAndLength($fullName, self::FULL_NAME_LENGTH_MAX);
		}
		if ($titleBefore !== null) {
			Validator::checkWhitespacesAndLength($titleBefore, self::TITLE_LENGTH_MAX);
		}
		if ($titleAfter !== null) {
			Validator::checkWhitespacesAndLength($titleAfter, self::TITLE_LENGTH_MAX);
		}
		if ($tin !== null) {
			Validator::checkWhitespacesAndLength($tin, self::TIN_VATIN_LENGTH_MAX);
		}
		if ($vatin !== null) {
			Validator::checkWhitespacesAndLength($vatin, self::TIN_VATIN_LENGTH_MAX);
		}

		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->fullName = $fullName;
		$this->titleBefore = $titleBefore;
		$this->titleAfter = $titleAfter;
		$this->email = $email;
		$this->phone = $phone;
		$this->tin = $tin;
		$this->vatin = $vatin;
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		$data = [
			'email' => $this->email,
			'phone' => $this->phone,
		];

		if ($this->fullName !== null) {
			$data['fullName'] = $this->fullName;
		} else {
			$data['firstName'] = $this->firstName;
			$data['lastName'] = $this->lastName;
		}

		if ($this->titleBefore !== null) {
			$data['titleBefore'] = $this->titleBefore;
		}
		if ($this->titleAfter !== null) {
			$data['titleAfter'] = $this->titleAfter;
		}
		if ($this->tin !== null) {
			$data['tin'] = $this->tin;
		}
		if ($this->vatin !== null) {
			$data['vatin'] = $this->vatin;
		}

		return $data;
	}

	public function getFirstName(): ?string
	{
		return $this->firstName;
	}

	public function getLastName(): ?string
	{
		return $this->lastName;
	}

	public function getFullName(): ?string
	{
		return $this->fullName;
	}

	public function getTitleBefore(): ?string
	{
		return $this->titleBefore;
	}

	public function getTitleAfter(): ?string
	{
		return $this->titleAfter;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function getPhone(): string
	{
		return $this->phone;
	}

	public function getTin(): ?string
	{
		return $this->tin;
	}

	public function getVatin(): ?string
	{
		return $this->vatin;
	}

}
