<?php

namespace SlevomatCsobGateway\Api\Driver;

class CurlDriverException extends \RuntimeException
{

	/**
	 * @var mixed
	 */
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
