<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{

	public function testCheckCartItemName()
	{
		Validator::checkCartItemName('foo name');

		try {
			Validator::checkCartItemName('very long long long cart item name');
			$this->fail();

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('Cart item name can have maximum of 20 characters.', $e->getMessage());
		}

		try {
			Validator::checkCartItemName(' whitespace');
			$this->fail();

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('Argument starts or ends with whitespace.', $e->getMessage());
		}
	}

	public function testCheckCartItemDescription()
	{
		Validator::checkCartItemDescription('foo description');

		try {
			Validator::checkCartItemDescription('very long long long cart item description');
			$this->fail();

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('Cart item description can have maximum of 40 characters.', $e->getMessage());
		}
	}

	public function testCheckCartItemQuantity()
	{
		Validator::checkCartItemQuantity(2);

		try {
			Validator::checkCartItemQuantity(0);
			$this->fail();

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('Quantity must be greater than 0. 0 given.', $e->getMessage());
		}
	}

	public function testCheckOrderId()
	{
		Validator::checkOrderId('123');

		try {
			Validator::checkOrderId('123456789123456789');
			$this->fail();

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('OrderId can have maximum of 10 characters.', $e->getMessage());
		}

		try {
			Validator::checkOrderId('abc');
			$this->fail();

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('OrderId must be numeric value. abc given.', $e->getMessage());
		}
	}

	public function testCheckReturnUrl()
	{
		Validator::checkReturnUrl('https://example.com');

		try {
			Validator::checkReturnUrl('https://example.com/' . implode('-', array_fill(0, 100, 'long')));
			$this->fail();

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('ReturnUrl can have maximum of 300 characters.', $e->getMessage());
		}
	}

	public function testDescription()
	{
		Validator::checkDescription('foo description');

		try {
			Validator::checkDescription(implode(' ', array_fill(0, 60, 'very')) . ' long description');
			$this->fail();

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('Description can have maximum of 255 characters.', $e->getMessage());
		}
	}

	public function testMerchantData()
	{
		Validator::checkMerchantData('foo merchant data');

		try {
			Validator::checkMerchantData(implode(' ', array_fill(0, 60, 'very')) . ' long merchantData');
			$this->fail();

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('MerchantData can have maximum of 255 characters in encoded state.', $e->getMessage());
		}
	}

	public function testCustomerId()
	{
		Validator::checkCustomerId('foo customerId');

		try {
			Validator::checkCustomerId('very very very very very very very very long long long customerId');
			$this->fail();

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('CustomerId can have maximum of 50 characters.', $e->getMessage());
		}
	}

	public function testPayId()
	{
		Validator::checkPayId('foo payId');

		try {
			Validator::checkPayId('very long long payId');
			$this->fail();

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('PayId can have maximum of 15 characters.', $e->getMessage());
		}
	}

	public function testTtlSec()
	{
		Validator::checkTtlSec(500);

		try {
			Validator::checkTtlSec(200);
			$this->fail();

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('TTL sec is out of range (300 - 1800). Current value is 200.', $e->getMessage());
		}

		try {
			Validator::checkTtlSec(3000);
			$this->fail();

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('TTL sec is out of range (300 - 1800). Current value is 3000.', $e->getMessage());
		}
	}

}
