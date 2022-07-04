<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\Validator;
use function array_filter;

class EchoCustomerResponse implements Response
{

	public function __construct(
		private string $customerId,
		private DateTimeImmutable $responseDateTime,
		private ResultCode $resultCode,
		private string $resultMessage,
	)
	{
		Validator::checkCustomerId($customerId);
	}

	/**
	 * @param mixed[] $data
	 */
	public static function createFromResponseData(array $data): self
	{
		return new self(
			$data['customerId'],
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
			'customerId' => null,
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
			'customerId' => $this->customerId,
			'dttm' => $this->responseDateTime->format('YmdHis'),
			'resultCode' => $this->resultCode->value,
			'resultMessage' => $this->resultMessage,
		], EncodeHelper::filterValueCallback());
	}

	public function getCustomerId(): string
	{
		return $this->customerId;
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
