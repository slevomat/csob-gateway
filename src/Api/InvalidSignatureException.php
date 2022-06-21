<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

use RuntimeException;

class InvalidSignatureException extends RuntimeException
{

	/**
	 * @param mixed[] $responseData
	 */
	public function __construct(private array $responseData)
	{
		parent::__construct('Invalid signature.');
	}

	/**
	 * @return mixed[]
	 */
	public function getResponseData(): array
	{
		return $this->responseData;
	}

}
