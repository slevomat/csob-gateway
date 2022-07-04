<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\AdditionalData;

use DateTimeImmutable;
use SlevomatCsobGateway\Encodable;
use SlevomatCsobGateway\EncodeHelper;
use function array_filter;
use function in_array;
use const DATE_ATOM;

class CustomerLogin implements Encodable
{

	private ?string $authData = null;

	public function __construct(
		private ?CustomerLoginAuth $auth = null,
		private ?DateTimeImmutable $authAt = null,
		?string $authData = null,
	)
	{
		$allowedAuthDataFor = [
			CustomerLoginAuth::FEDERATED,
			CustomerLoginAuth::FIDO,
			CustomerLoginAuth::FIDO_SIGNED,
			CustomerLoginAuth::API,
		];

		if ($authData !== null && in_array($this->auth, $allowedAuthDataFor, true)) {
			$this->authData = $authData;
		}
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'auth' => $this->auth?->value,
			'authAt' => $this->authAt?->format(DATE_ATOM),
			'authData' => $this->authData,
		], EncodeHelper::filterValueCallback());
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'auth' => null,
			'authAt' => null,
			'authData' => null,
		];
	}

}
