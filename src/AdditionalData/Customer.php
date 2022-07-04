<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\AdditionalData;

use SlevomatCsobGateway\Encodable;
use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\Validator;
use function array_filter;

class Customer implements Encodable
{

	public const NAME_LENGTH_MAX = 45;
	public const EMAIL_LENGTH_MAX = 100;

	public function __construct(
		private ?string $name = null,
		private ?string $email = null,
		private ?string $homePhone = null,
		private ?string $workPhone = null,
		private ?string $mobilePhone = null,
		private ?CustomerAccount $customerAccount = null,
		private ?CustomerLogin $customerLogin = null,
	)
	{
		if ($this->name !== null) {
			Validator::checkWhitespacesAndLength($this->name, self::NAME_LENGTH_MAX);
		}
		if ($this->email !== null) {
			Validator::checkWhitespacesAndLength($this->email, self::EMAIL_LENGTH_MAX);
			Validator::checkEmail($this->email);
		}
		if ($this->homePhone !== null) {
			Validator::checkPhone($this->homePhone);
		}
		if ($this->workPhone !== null) {
			Validator::checkPhone($this->workPhone);
		}
		if ($this->mobilePhone !== null) {
			Validator::checkPhone($this->mobilePhone);
		}
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'name' => $this->name,
			'email' => $this->email,
			'homePhone' => $this->homePhone,
			'workPhone' => $this->workPhone,
			'mobilePhone' => $this->mobilePhone,
			'account' => $this->customerAccount?->encode(),
			'login' => $this->customerLogin?->encode(),
		], EncodeHelper::filterValueCallback());
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'name' => null,
			'email' => null,
			'homePhone' => null,
			'workPhone' => null,
			'mobilePhone' => null,
			'account' => CustomerAccount::encodeForSignature(),
			'login' => CustomerLogin::encodeForSignature(),
		];
	}

}
