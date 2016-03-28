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

	/**
	 * @param ResponseCode $responseCode
	 * @param mixed[]|null $data
	 * @param string[] $headers
	 */
	public function __construct(
		ResponseCode $responseCode,
		array $data = null,
		array $headers = []
	)
	{
		$this->responseCode = $responseCode;
		$this->data = $data;
		$this->headers = $headers;
	}

	public function getResponseCode(): ResponseCode
	{
		return $this->responseCode;
	}

	/**
	 * @return mixed[]|null
	 */
	public function getData()
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

}
