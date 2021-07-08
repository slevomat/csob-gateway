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

	/** @var mixed[] */
	private $keysPriority;

	/**
	 * @param mixed[] $keysPriority
	 */
	public function __construct(array $keysPriority)
	{
		$this->keysPriority = $keysPriority;
	}

	/**
	 * @param mixed[] $data
	 * @return string
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
			if (!array_key_exists($key, $data)) {
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

	/**
	 * @param int|bool|string|float|null $value
	 * @return string
	 */
	private function formatSingleValue($value): string
	{
		if (is_bool($value)) {
			return $value ? 'true' : 'false';
		}

		return (string) $value;
	}

}
