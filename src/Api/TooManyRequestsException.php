<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

class TooManyRequestsException extends RequestException
{

	public function __construct(Response $response)
	{
		parent::__construct('Too Many Requests', $response);
	}

}
