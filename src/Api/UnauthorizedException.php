<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

class UnauthorizedException extends RequestException
{

	public function __construct(Response $response)
	{
		parent::__construct('Unauthorized', $response);
	}

}
