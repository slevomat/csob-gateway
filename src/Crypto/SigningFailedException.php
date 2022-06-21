<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Crypto;

use RuntimeException;

class SigningFailedException extends RuntimeException
{

	/**
	 * @param mixed[] $data
	 */
	public function __construct(private array $data)
	{
		parent::__construct('Signing failed');
	}

	/**
	 * @return mixed[]
	 */
	public function getData(): array
	{
		return $this->data;
	}

}
