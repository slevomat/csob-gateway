<?php

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
			$this->assertSame('Name length must be less than 20 symbols.', $e->getMessage());
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
			$this->assertSame('Description length must be less than 40 symbols.', $e->getMessage());
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
			$this->assertSame('OrderId length must be less than 10 symbols.', $e->getMessage());
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
			$this->assertSame('ReturnUrl length must be less than 300 symbols.', $e->getMessage());
		}
	}

	public function testDescription()
	{
		Validator::checkDescription('foo description');

		try {
			Validator::checkDescription(implode(' ', array_fill(0, 60, 'very')) . ' long description');
			$this->fail();

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('Description length must be less than 255 symbols.', $e->getMessage());
		}
	}

	public function testMerchantData()
	{
		Validator::checkMerchantData('foo merchant data');

		try {
			Validator::checkMerchantData(implode(' ', array_fill(0, 60, 'very')) . ' long merchantData');
			$this->fail();

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('MerchantData length must be less than 255 symbols.', $e->getMessage());
		}
	}

	public function testCustomerId()
	{
		Validator::checkCustomerId('foo customerId');

		try {
			Validator::checkCustomerId('very very very very very very very very long long long customerId');
			$this->fail();

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('CustomerId length must be less than 50 symbols.', $e->getMessage());
		}
	}

	public function testPayId()
	{
		Validator::checkPayId('foo payId');

		try {
			Validator::checkPayId('very long long payId');
			$this->fail();

		} catch (\InvalidArgumentException $e) {
			$this->assertSame('PayId length must be less than 15 symbols.', $e->getMessage());
		}
	}

}
