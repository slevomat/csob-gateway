<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

interface ApiClientDriver
{

	/**
	 * @param mixed[]|null $data
	 * @param string[] $headers
	 */
	public function request(HttpMethod $method, string $url, ?array $data, array $headers = []): Response;

}
