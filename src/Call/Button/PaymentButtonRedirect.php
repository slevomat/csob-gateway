<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Button;

use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Validator;

class PaymentButtonRedirect
{

	/**
	 * @param mixed[]|null $params
	 */
	public function __construct(
		private HttpMethod $method,
		private string $url,
		private ?array $params = null,
	)
	{
		Validator::checkReturnMethod($this->method);
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'method' => null,
			'url' => null,
			'params' => null,
		];
	}

	public function getMethod(): ?HttpMethod
	{
		return $this->method;
	}

	public function getUrl(): ?string
	{
		return $this->url;
	}

	/**
	 * @return mixed[]|null
	 */
	public function getParams(): ?array
	{
		return $this->params;
	}

}
