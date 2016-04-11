<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

class InvalidSignatureException extends \RuntimeException
{

	/** @var mixed[] */
	private $responseData;

	public function __construct(array $responseData)
	{
		parent::__construct('Invalid signature.');
		$this->responseData = $responseData;
	}

	public function getResponseData(): array
	{
		return $this->responseData;
	}

}
