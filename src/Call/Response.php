<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Encodable;

interface Response extends Encodable
{

	/**
	 * @param mixed[] $data
	 */
	public static function createFromResponseData(array $data): self;

}
