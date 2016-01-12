<?php

namespace SlevomatCsobGateway\Api;

class InternalErrorException extends RequestException
{

	public function __construct(Response $response)
	{
		parent::__construct('Internal Error Exception', $response);
	}

}
