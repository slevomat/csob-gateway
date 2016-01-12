<?php

namespace SlevomatCsobGateway\Api;

abstract class RequestException extends \RuntimeException
{

	/**
	 * @var Response
	 */
	private $response;

	/**
	 * @param string $message
	 * @param Response $response
	 */
	public function __construct($message, Response $response)
	{
		parent::__construct($message);

		$this->response = $response;
	}

	/**
	 * @return Response
	 */
	public function getResponse()
	{
		return $this->response;
	}

}
