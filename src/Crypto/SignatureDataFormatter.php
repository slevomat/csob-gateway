<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Crypto;

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
				$message = array_merge($message, $this->generateMessage($data[$key], $values));
			} else {
				if (is_bool($data[$key])) {
					$message[] = $data[$key]
						? 'true'
						: 'false';
				} else {
					$message[] = $data[$key];
				}
			}
		}

		return $message;
	}

}
