<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

use RuntimeException;

abstract class RequestException extends RuntimeException
{

	public function __construct(string $message, private Response $response)
	{
		parent::__construct($message);
	}

	public function getResponse(): Response
	{
		return $this->response;
	}

}
