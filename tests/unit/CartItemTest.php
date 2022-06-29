<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CartItemTest extends TestCase
{

	public function testValidation(): void
	{
		try {
			new CartItem('foo name', 0, 99);
			self::fail();

		} catch (InvalidArgumentException $e) {
			self::assertSame('Quantity must be greater than 0. 0 given.', $e->getMessage());
		}

		try {
			new CartItem('very long long long cart item name', 1, 99);
			self::fail();

		} catch (InvalidArgumentException $e) {
			self::assertSame('Cart item name can have maximum of 20 characters.', $e->getMessage());
		}

		try {
			new CartItem('foo name', 1, 99, 'very long long long long long long long long long long cart item description');
			self::fail();

		} catch (InvalidArgumentException $e) {
			self::assertSame('Cart item description can have maximum of 40 characters.', $e->getMessage());
		}
	}

}
