<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

use PHPUnit\Framework\TestCase;

class PriceTest extends TestCase
{

	public function testEncode(): void
	{
		$price = new Price(123, Currency::USD);

		self::assertSame(['amount' => 123, 'currency' => 'USD'], $price->encode());
	}

}
