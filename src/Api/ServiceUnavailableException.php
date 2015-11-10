<?php

namespace SlevomatCsobGateway\Api;

class ServiceUnavailableException extends \RuntimeException implements RequestException
{

	/**
	 * @var Response
	 */
	private $response;

	public function __construct(Response $response)
	{
		parent::__construct('Service Unavailable');

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
