<?php

namespace SlevomatCsobGateway\Crypto;

class SigningFailedException extends \RuntimeException
{

	/**
	 * @var mixed[]
	 */
	private $data;

	/**
	 * @param mixed[] $data
	 */
	public function __construct(array $data)
	{
		parent::__construct('Signing failed');

		$this->data = $data;
	}

	/**
	 * @return mixed[]
	 */
	public function getData()
	{
		return $this->data;
	}

}
