<?php

namespace SlevomatCsobGateway\Api;

class ForbiddenException extends \RuntimeException implements RequestException
{

	/**
	 * @var Response
	 */
	private $response;

	public function __construct(Response $response)
	{
		parent::__construct('Forbidden');

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
