<?php

namespace SlevomatCsobGateway\Type;

class InvalidEnumValueException extends \InvalidArgumentException
{

	/**
	 * @var mixed
	 */
	private $value;

	/**
	 * @var mixed[]
	 */
	private $availableValues;

	/**
	 * @param mixed $value
	 * @param mixed[] $availableValues
	 */
	public function __construct($value, array $availableValues)
	{
		parent::__construct(sprintf(
			'Invalid enum value \'%s\'. Available values: %s',
			$value,
			join(', ', $availableValues)
		));

		$this->value = $value;
		$this->availableValues = $availableValues;
	}

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return mixed[]
	 */
	public function getAvailableValues()
	{
		return $this->availableValues;
	}

}
