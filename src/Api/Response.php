<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

class Response
{

	/**
	 * @param mixed[]|null $data
	 * @param string[]|string[][] $headers
	 * @param mixed[] $extensions
	 */
	public function __construct(
		private ResponseCode $responseCode,
		private ?array $data = null,
		private array $headers = [],
		private array $extensions = [],
	)
	{
	}

	public function getResponseCode(): ResponseCode
	{
		return $this->responseCode;
	}

	/**
	 * @return mixed[]|null
	 */
	public function getData(): ?array
	{
		return $this->data;
	}

	/**
	 * @return string[]|string[][]
	 */
	public function getHeaders(): array
	{
		return $this->headers;
	}

	/**
	 * @return mixed[]
	 */
	public function getExtensions(): array
	{
		return $this->extensions;
	}

}
