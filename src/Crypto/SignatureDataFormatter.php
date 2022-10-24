<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Crypto;

use function array_key_exists;
use function array_merge;
use function implode;
use function is_array;
use function is_bool;
use function is_int;

class SignatureDataFormatter
{

	/**
	 * @param mixed[] $keysPriority
	 */
	public function __construct(private array $keysPriority)
	{
	}

	/**
	 * @param mixed[] $data
	 */
	public function formatDataForSignature(array $data): string
	{
		return implode('|', $this->generateMessage($data, $this->keysPriority));
	}

	/**
	 * @param mixed[] $data
	 * @param mixed[] $keys
	 * @return mixed[]
	 */
	private function generateMessage(array $data, array $keys): array
	{
		$message = [];

		foreach ($keys as $key => $values) {
			if (is_int($key)) {
				foreach ($data as $items) {
					$message = array_merge($message, $this->generateMessage($items, $values));
				}
				continue;
			}
			if (!array_key_exists($key, $data) || $data[$key] === null) {
				continue;
			}

			if (is_array($values)) {
				if ($values === []) {
					$listData = (array) $data[$key];
					foreach ($listData as $value) {
						$message[] = $this->formatSingleValue($value);
					}
				} else {
					$message = array_merge($message, $this->generateMessage($data[$key], $values));
				}
			} else {
				$message[] = $this->formatSingleValue($data[$key]);
			}
		}

		return $message;
	}

	private function formatSingleValue(int|bool|string|float|null $value): string
	{
		if (is_bool($value)) {
			return $value ? 'true' : 'false';
		}

		return (string) $value;
	}

}
