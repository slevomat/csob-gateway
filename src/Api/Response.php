<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

class Response
{

	/**
	 * @var ResponseCode
	 */
	private $responseCode;

	/**
	 * @var mixed[]|null
	 */
	private $data;

	/**
	 * @var string[]
	 */
	private $headers;

	/** @var mixed[] */
	private $extensions;

	/**
	 * @param ResponseCode $responseCode
	 * @param mixed[]|null $data
	 * @param string[] $headers
	 * @param mixed[] $extensions
	 */
	public function __construct(
		ResponseCode $responseCode,
		?array $data,
		array $headers = [],
		array $extensions = []
	)
	{
		$this->responseCode = $responseCode;
		$this->data = $data;
		$this->headers = $headers;
		$this->extensions = $extensions;
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
	 * @return string[]
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
