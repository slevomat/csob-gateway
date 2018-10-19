<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api\Driver;

use RuntimeException;
use SlevomatCsobGateway\Api\ApiClientDriverException;

class CurlDriverException extends RuntimeException implements ApiClientDriverException
{

	/** @var mixed */
	private $info;

	/**
	 * @param resource $handle
	 */
	public function __construct($handle)
	{
		parent::__construct('Request error: ' . curl_error($handle));

		$this->code = curl_errno($handle);
		$this->info = curl_getinfo($handle);
	}
	/**
	 * @see curl_getinfo()
	 * @return mixed
	 */
	public function getInfo()
	{
		return $this->info;
	}

}
