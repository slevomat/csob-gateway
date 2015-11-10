<?php

namespace SlevomatCsobGateway\Api;

interface RequestException
{

	/**
	 * @return Response
	 */
	public function getResponse();

}
