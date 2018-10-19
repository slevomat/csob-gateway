<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

abstract class RequestException extends \RuntimeException
{

	/** @var Response */
	private $response;

	public function __construct(string $message, Response $response)
	{
		parent::__construct($message);

		$this->response = $response;
	}

	public function getResponse(): Response
	{
		return $this->response;
	}

}
