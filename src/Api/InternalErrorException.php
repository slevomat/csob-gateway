<?php

namespace SlevomatCsobGateway\Api;

class InternalErrorException extends \RuntimeException implements RequestException
{

	/**
	 * @var Response
	 */
	private $response;

	public function __construct(Response $response)
	{
		parent::__construct('Internal Error Exception');

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
