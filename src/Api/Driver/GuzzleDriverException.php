<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api\Driver;

use RuntimeException;
use SlevomatCsobGateway\Api\ApiClientDriverException;
use Throwable;

class GuzzleDriverException extends RuntimeException implements ApiClientDriverException
{

	public function __construct(Throwable $previous)
	{
		parent::__construct('Request error: ' . $previous->getMessage(), $previous->getCode(), $previous);
	}

}
