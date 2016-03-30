<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

class MethodNotAllowedException extends RequestException
{

	public function __construct(Response $response)
	{
		parent::__construct('Method Not Allowed', $response);
	}

}
