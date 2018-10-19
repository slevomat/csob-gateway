<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

class PriceTest extends \PHPUnit\Framework\TestCase
{

	public function testGetters(): void
	{
		$cartItem = new Price(123, Currency::get(Currency::USD));

		self::assertSame(123, $cartItem->getAmount());
		self::assertSame(Currency::USD, $cartItem->getCurrency()->getValue());
	}

}
