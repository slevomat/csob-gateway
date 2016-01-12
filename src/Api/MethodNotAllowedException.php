<?php

namespace SlevomatCsobGateway\Api;

class MethodNotAllowedException extends RequestException
{

	public function __construct(Response $response)
	{
		parent::__construct('Method Not Allowed', $response);
	}

}
