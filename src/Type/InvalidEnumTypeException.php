<?php

namespace SlevomatCsobGateway\Type;

class InvalidEnumTypeException extends \InvalidArgumentException
{

	/**
	 * @var Enum
	 */
	private $enum;

	/**
	 * @var string
	 */
	private $expectedClass;

	/**
	 * @param Enum $enum
	 * @param string $expectedClass
	 */
	public function __construct(Enum $enum, $expectedClass)
	{
		parent::__construct(sprintf(
			'Invalid enum type \'%s\'. Expected class: %s',
			get_class($enum),
			$expectedClass
		));

		$this->enum = $enum;
		$this->expectedClass = $expectedClass;
	}

	/**
	 * @return Enum
	 */
	public function getEnum()
	{
		return $this->enum;
	}

	/**
	 * @return string
	 */
	public function getExpectedClass()
	{
		return $this->expectedClass;
	}

}
