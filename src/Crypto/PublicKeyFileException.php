<?php

namespace SlevomatCsobGateway\Crypto;

class PublicKeyFileException extends \RuntimeException
{

	/**
	 * @var string
	 */
	private $publicKeyFile;

	public function __construct($publicKeyFile, \Exception $previous = null)
	{
		parent::__construct(sprintf(
			'Public key could not be loaded from file \'%s\'. Please make sure that the file contains valid public key.',
			$publicKeyFile
		), 0, $previous);

		$this->publicKeyFile = $publicKeyFile;
	}

	/**
	 * @return string
	 */
	public function getPublicKeyFile()
	{
		return $this->publicKeyFile;
	}

}
