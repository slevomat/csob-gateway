<?php

namespace SlevomatCsobGateway\Api;

class InvalidSignatureException extends RequestException
{

	public function __construct(Response $response)
	{
		parent::__construct('Invalid signature.', $response);
	}

}
