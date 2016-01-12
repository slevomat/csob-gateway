<?php

namespace SlevomatCsobGateway\Api;

class ServiceUnavailableException extends RequestException
{

	public function __construct(Response $response)
	{
		parent::__construct('Service Unavailable', $response);
	}

}
