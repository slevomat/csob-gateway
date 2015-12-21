<?php

namespace SlevomatCsobGateway\Api;

interface ApiClientDriver
{

	/**
	 * @param HttpMethod $method
	 * @param string $url
	 * @param mixed[]|null $data
	 * @param string[] $headers
	 * @return Response
	 */
	public function request(HttpMethod $method, $url, array $data = null, array $headers = []);

}
