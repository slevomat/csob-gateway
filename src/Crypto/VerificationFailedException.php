<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Crypto;

use RuntimeException;
use function sprintf;

class VerificationFailedException extends RuntimeException
{

	/**
	 * @param mixed[] $data
	 */
	public function __construct(private array $data, private string $errorMessage)
	{
		parent::__construct(sprintf(
			'Verification failed: %s',
			$errorMessage,
		));
	}

	/**
	 * @return mixed[]
	 */
	public function getData(): array
	{
		return $this->data;
	}

	public function getErrorMessage(): string
	{
		return $this->errorMessage;
	}

}
