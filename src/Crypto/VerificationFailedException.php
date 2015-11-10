<?php

namespace SlevomatCsobGateway\Crypto;

class VerificationFailedException extends \RuntimeException
{

	/**
	 * @var mixed[]
	 */
	private $data;

	/**
	 * @var string
	 */
	private $errorMessage;

	/**
	 * @param mixed[] $data
	 * @param string $errorMessage
	 */
	public function __construct(array $data, $errorMessage)
	{
		parent::__construct(sprintf(
			'Verification failed: %s',
			$errorMessage
		));

		$this->data = $data;
		$this->errorMessage = $errorMessage;
	}

	/**
	 * @return mixed[]
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @return string
	 */
	public function getErrorMessage()
	{
		return $this->errorMessage;
	}

}
