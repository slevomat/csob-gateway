<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

class EncodeHelper
{

	public static function filterValueCallback(): callable
	{
		return static fn (mixed $value): bool => $value !== null && $value !== [];
	}

}
