<?php

namespace SlevomatCsobGateway\Api;

class TooManyRequestsException extends \RuntimeException implements RequestException
{

	/**
	 * @var Response
	 */
	private $response;

	public function __construct(Response $response)
	{
		parent::__construct('Too Many Requests');

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
