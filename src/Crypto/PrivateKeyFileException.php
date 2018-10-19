<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Crypto;

use RuntimeException;
use Throwable;
use function sprintf;

class PrivateKeyFileException extends RuntimeException
{

	/** @var string */
	private $privateKeyFile;

	public function __construct(string $privateKeyFile, ?Throwable $previous = null)
	{
		parent::__construct(sprintf(
			'Private key could not be loaded from file \'%s\'. Please make sure that the file contains valid private key in PEM format.',
			$privateKeyFile
		), 0, $previous);

		$this->privateKeyFile = $privateKeyFile;
	}

	public function getPrivateKeyFile(): string
	{
		return $this->privateKeyFile;
	}

}
