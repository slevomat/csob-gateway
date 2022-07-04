<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\EncodeHelper;
use function array_filter;

class EchoResponse implements Response
{

	public function __construct(
		private DateTimeImmutable $responseDateTime,
		private ResultCode $resultCode,
		private string $resultMessage,
	)
	{
	}

	/**
	 * @param mixed[] $data
	 */
	public static function createFromResponseData(array $data): self
	{
		return new self(
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			ResultCode::from($data['resultCode']),
			$data['resultMessage'],
		);
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'dttm' => null,
			'resultCode' => null,
			'resultMessage' => null,
		];
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'dttm' => $this->responseDateTime->format('YmdHis'),
			'resultCode' => $this->resultCode->value,
			'resultMessage' => $this->resultMessage,
		], EncodeHelper::filterValueCallback());
	}

	public function getResponseDateTime(): DateTimeImmutable
	{
		return $this->responseDateTime;
	}

	public function getResultCode(): ResultCode
	{
		return $this->resultCode;
	}

	public function getResultMessage(): string
	{
		return $this->resultMessage;
	}

}
