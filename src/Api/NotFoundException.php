<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

class NotFoundException extends RequestException
{

	public function __construct(Response $response)
	{
		parent::__construct('Not Found', $response);
	}

}
