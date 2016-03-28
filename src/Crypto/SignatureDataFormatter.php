<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Crypto;

class SignatureDataFormatter
{

	/** @var mixed[] */
	private $keysPriority;

	public function __construct(array $keysPriority)
	{
		$this->keysPriority = $keysPriority;
	}

	public function formatDataForSignature(array $data): string
	{
		return implode('|', $this->generateMessage($data, $this->keysPriority));
	}

	private function generateMessage(array $data, array $keys): array
	{
		$message = [];

		foreach ($keys as $key => $values) {
			if (!array_key_exists($key, $data)) {
				continue;
			}

			if (is_array($values)) {
				foreach ($data[$key] as $subData) {
					$message = array_merge($message, $this->generateMessage($subData, $values));
				}

			} else {
				$message[] = is_bool($data[$key])
					? ($data[$key] ? 'true' : 'false')
					: $data[$key];
			}
		}

		return $message;
	}

}
