<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Crypto;

use RuntimeException;
use Throwable;
use function sprintf;

class PublicKeyFileException extends RuntimeException
{

	public function __construct(private string $publicKeyFile, ?Throwable $previous = null)
	{
		parent::__construct(sprintf(
			'Public key could not be loaded from file \'%s\'. Please make sure that the file contains valid public key.',
			$publicKeyFile,
		), 0, $previous);
	}

	public function getPublicKeyFile(): string
	{
		return $this->publicKeyFile;
	}

}
