<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

class InternalErrorException extends RequestException
{

	public function __construct(Response $response)
	{
		parent::__construct(sprintf('Internal Error - response code %d', $response->getResponseCode()->getValue()), $response);
	}

}
