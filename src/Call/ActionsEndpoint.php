<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use InvalidArgumentException;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Encodable;
use SlevomatCsobGateway\EncodeHelper;
use function array_filter;

class ActionsEndpoint implements Encodable
{

	/**
	 * @param mixed[] $vars
	 */
	public function __construct(
		private string $url,
		private ?HttpMethod $method = null,
		private ?array $vars = null,
	)
	{
		if ($this->method !== null && $this->method !== HttpMethod::GET && $this->method !== HttpMethod::POST) {
			throw new InvalidArgumentException('Only GET or POST are allowed.');
		}
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'url' => null,
			'method' => null,
			'vars' => [],
		];
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'url' => $this->url,
			'method' => $this->method?->value,
			'vars' => $this->vars,
		], EncodeHelper::filterValueCallback());
	}

	public function getUrl(): string
	{
		return $this->url;
	}

	public function getMethod(): ?HttpMethod
	{
		return $this->method;
	}

	/**
	 * @return mixed[]|null
	 */
	public function getVars(): ?array
	{
		return $this->vars;
	}

}
