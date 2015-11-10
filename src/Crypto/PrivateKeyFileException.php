<?php

namespace SlevomatCsobGateway\Crypto;

class PrivateKeyFileException extends \RuntimeException
{

	/**
	 * @var string
	 */
	private $privateKeyFile;

	public function __construct($privateKeyFile, \Exception $previous = null)
	{
		parent::__construct(sprintf(
			'Private key could not be loaded from file \'%s\'. Please make sure that the file contains valid private key in PEM format.',
			$privateKeyFile
		), 0, $previous);

		$this->privateKeyFile = $privateKeyFile;
	}

	/**
	 * @return string
	 */
	public function getPrivateKeyFile()
	{
		return $this->privateKeyFile;
	}

}
