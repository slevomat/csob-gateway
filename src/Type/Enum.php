<?php

namespace SlevomatCsobGateway\Type;

use ReflectionClass;

abstract class Enum
{

	/**
	 * @var mixed
	 */
	private $value;

	/**
	 * @var mixed[]
	 */
	private static $availableValues;

	/**
	 * @param mixed $value
	 */
	public function __construct($value)
	{
		self::checkValue($value);
		$this->value = $value;
	}

	/**
	 * @param mixed $value
	 */
	private static function checkValue($value)
	{
		if (!self::isValidValue($value)) {
			throw new InvalidEnumValueException(
				$value,
				self::getAvailableValues()
			);
		}
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param Enum $enum
	 * @return bool
	 */
	public function equals(self $enum)
	{
		if (get_class($this) !== get_class($enum)) {
			throw new InvalidEnumTypeException($enum, get_class($this));
		}

		return $this->equalsValue($enum->getValue());
	}

	/**
	 * @param mixed $value
	 * @return bool
	 */
	public function equalsValue($value)
	{
		self::checkValue($value);

		return $this->getValue() === $value;
	}

	/**
	 * @param mixed $value
	 * @return bool
	 */
	private static function isValidValue($value)
	{
		return in_array($value, self::getAvailableValues(), true);
	}

	/**
	 * @return mixed[]
	 */
	private static function getAvailableValues()
	{
		$index = get_called_class();
		if (!isset(self::$availableValues[$index])) {
			$classReflection = new ReflectionClass(get_called_class());
			self::$availableValues[$index] = $classReflection->getConstants();
		}
		return self::$availableValues[$index];
	}

}
