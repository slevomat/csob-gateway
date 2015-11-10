<?php

namespace SlevomatCsobGateway\Api;

class NotFoundException extends \RuntimeException implements RequestException
{

	/**
	 * @var Response
	 */
	private $response;

	public function __construct(Response $response)
	{
		parent::__construct('Not Found');

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
