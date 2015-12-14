<?php

namespace SlevomatCsobGateway\Api\Driver;

class CurlDriverException extends \RuntimeException
{

	/**
	 * @var mixed
	 */
	private $info;

	/**
	 * @param string $curlError
	 * @param resource $handle
	 */
	public function __construct($curlError, $handle)
	{
		parent::__construct('Request error: ' . $curlError);
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
