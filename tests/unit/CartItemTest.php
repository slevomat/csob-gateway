<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

class CartItemTest extends \PHPUnit\Framework\TestCase
{

	public function testNullDescription()
	{
		$cartItem = new CartItem('foo name', 1, 99);

		$this->assertNull($cartItem->getDescription());
	}

	public function testValidation()
	{
		try {
			new CartItem('foo name', 0, 99);
			$this->fail();

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('Quantity must be greater than 0. 0 given.', $e->getMessage());
		}

		try {
			new CartItem('very long long long cart item name', 1, 99);
			$this->fail();

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('Cart item name can have maximum of 20 characters.', $e->getMessage());
		}

		try {
			new CartItem('foo name', 1, 99, 'very long long long long long long long long long long cart item description');
			$this->fail();

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('Cart item description can have maximum of 40 characters.', $e->getMessage());
		}
	}

}
