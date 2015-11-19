<?php

namespace SlevomatCsobGateway;

class CartItemTest extends \PHPUnit_Framework_TestCase
{

	public function testValidation()
	{
		try {
			new CartItem('foo name', 0, 99);

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('Quantity must be greater than 0. 99 given.', $e->getMessage());
		}

		try {
			new CartItem('very long long long cart item name', 0, 99);

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('Name length must be less than 20 symbols.', $e->getMessage());
		}
	}

}
