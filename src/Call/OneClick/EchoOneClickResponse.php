<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\OneClick;

use DateTimeImmutable;
use SlevomatCsobGateway\Call\Response;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\Validator;
use function array_filter;

class EchoOneClickResponse implements Response
{

	public function __construct(
		private string $origPayId,
		private DateTimeImmutable $responseDateTime,
		private ResultCode $resultCode,
		private string $resultMessage,
	)
	{
		Validator::checkPayId($this->origPayId);
	}

	/**
	 * @param mixed[] $data
	 */
	public static function createFromResponseData(array $data): self
	{
		return new self(
			$data['origPayId'],
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
			'origPayId' => null,
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
			'origPayId' => $this->origPayId,
			'dttm' => $this->responseDateTime->format('YmdHis'),
			'resultCode' => $this->resultCode->value,
			'resultMessage' => $this->resultMessage,
		], EncodeHelper::filterValueCallback());
	}

	public function getOrigPayId(): string
	{
		return $this->origPayId;
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
