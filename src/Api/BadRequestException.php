<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

class BadRequestException extends RequestException
{

	public function __construct(Response $response)
	{
		parent::__construct('Bad Request', $response);
	}

}
