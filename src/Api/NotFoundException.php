<?php

namespace SlevomatCsobGateway\Api;

class NotFoundException extends RequestException
{

	public function __construct(Response $response)
	{
		parent::__construct('Not Found', $response);
	}

}
