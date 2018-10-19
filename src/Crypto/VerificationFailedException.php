<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Crypto;

class VerificationFailedException extends \RuntimeException
{

	/** @var mixed[] */
	private $data;

	/** @var string */
	private $errorMessage;

	/**
	 * @param mixed[] $data
	 * @param string $errorMessage
	 */
	public function __construct(array $data, string $errorMessage)
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
	public function getData(): array
	{
		return $this->data;
	}

	public function getErrorMessage(): string
	{
		return $this->errorMessage;
	}

}
