<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api\Driver;

use RuntimeException;
use SlevomatCsobGateway\Api\ApiClientDriverException;

class CurlDriverException extends RuntimeException implements ApiClientDriverException
{

	/** @var mixed */
	private $info;

	/**
	 * @param int $code
	 * @param string $message
	 * @param mixed $info
	 */
	public function __construct(int $code, string $message, $info)
	{
		parent::__construct('Request error: ' . $message);

		$this->code = $code;
		$this->info = $info;
	}
	/**
	 * @see curl_getinfo()
	 *
	 * @return mixed
	 */
	public function getInfo()
	{
		return $this->info;
	}

}
