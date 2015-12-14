<?php

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
	public function formatDataForSignature(array $data)
	{
		return implode('|', $this->generateMessage($data, $this->keysPriority));
	}

	/**
	 * @param mixed[] $data
	 * @param mixed[] $keys
	 * @return mixed[]
	 */
	private function generateMessage(array $data, array $keys)
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
