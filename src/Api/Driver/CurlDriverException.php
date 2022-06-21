<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api\Driver;

use RuntimeException;
use SlevomatCsobGateway\Api\ApiClientDriverException;

class CurlDriverException extends RuntimeException implements ApiClientDriverException
{

	public function __construct(int $code, string $message, private mixed $info)
	{
		parent::__construct('Request error: ' . $message);

		$this->code = $code;
	}

	/**
	 * @see curl_getinfo()
	 */
	public function getInfo(): mixed
	{
		return $this->info;
	}

}
