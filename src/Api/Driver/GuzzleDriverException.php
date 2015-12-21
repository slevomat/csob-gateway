<?php

namespace SlevomatCsobGateway\Api\Driver;

use SlevomatCsobGateway\Api\ApiClientDriverException;

class GuzzleDriverException extends \RuntimeException implements ApiClientDriverException
{

	public function __construct(\Exception $e)
	{
		parent::__construct('Request error: ' . $e->getMessage(), $e->getCode(), $e);
	}
}
