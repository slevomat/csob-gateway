<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Crypto;

class SigningFailedException extends \RuntimeException
{

	/**
	 * @var mixed[]
	 */
	private $data;

	public function __construct(array $data)
	{
		parent::__construct('Signing failed');

		$this->data = $data;
	}

	public function getData(): array
	{
		return $this->data;
	}

}
