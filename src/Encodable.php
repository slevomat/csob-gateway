<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

interface Encodable
{

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array;

	/**
	 * @return mixed[]
	 */
	public function encode(): array;

}
