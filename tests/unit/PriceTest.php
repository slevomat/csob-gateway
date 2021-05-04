<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PriceTest extends TestCase
{

	public function testGetters(): void
	{
		$price = new Price(123, Currency::get(Currency::USD));

		self::assertSame(123, $price->getAmount());
		self::assertSame(Currency::USD, $price->getCurrency()->getValue());
	}

	public function testEncode(): void
	{
		$price = new Price(123, Currency::get(Currency::USD));

		self::assertSame(['amount' => 123, 'currency' => 'USD'], $price->encode());
	}

	public function testValidation(): void
	{
		try {
			new Price(-123, Currency::get(Currency::USD));
			self::fail();
		} catch (InvalidArgumentException $e) {
			self::assertSame('Value is negative.', $e->getMessage());
		}
	}

}
