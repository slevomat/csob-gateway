<?php

namespace SlevomatCsobGateway\Api;

interface ApiClientDriver
{

	/**
	 * @param HttpMethod $method
	 * @param string $url
	 * @param string[] $queries
	 * @param mixed[]|null $data
	 * @param string[] $headers
	 * @return Response
	 */
	public function request(HttpMethod $method, $url, array $queries = [], array $data = null, array $headers = []);

}
