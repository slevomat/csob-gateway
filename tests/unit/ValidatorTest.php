<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use function array_fill;
use function implode;

class ValidatorTest extends TestCase
{

	public function testCheckCartItemName(): void
	{
		Validator::checkCartItemName('foo name');

		try {
			Validator::checkCartItemName('very long long long cart item name');
			self::fail();

		} catch (InvalidArgumentException $e) {
			self::assertSame('Cart item name can have maximum of 20 characters.', $e->getMessage());
		}

		try {
			Validator::checkCartItemName(' whitespace');
			self::fail();

		} catch (InvalidArgumentException $e) {
			self::assertSame('Argument starts or ends with whitespace.', $e->getMessage());
		}
	}

	public function testCheckCartItemDescription(): void
	{
		Validator::checkCartItemDescription('foo description');

		try {
			Validator::checkCartItemDescription('very long long long cart item description');
			self::fail();

		} catch (InvalidArgumentException $e) {
			self::assertSame('Cart item description can have maximum of 40 characters.', $e->getMessage());
		}
	}

	public function testCheckCartItemQuantity(): void
	{
		Validator::checkCartItemQuantity(2);

		try {
			Validator::checkCartItemQuantity(0);
			self::fail();

		} catch (InvalidArgumentException $e) {
			self::assertSame('Quantity must be greater than 0. 0 given.', $e->getMessage());
		}
	}

	public function testCheckOrderId(): void
	{
		Validator::checkOrderId('123');

		try {
			Validator::checkOrderId('123456789123456789');
			self::fail();

		} catch (InvalidArgumentException $e) {
			self::assertSame('OrderId can have maximum of 10 characters.', $e->getMessage());
		}

		try {
			Validator::checkOrderId('abc');
			self::fail();

		} catch (InvalidArgumentException $e) {
			self::assertSame('OrderId must be numeric value. abc given.', $e->getMessage());
		}
	}

	public function testCheckReturnUrl(): void
	{
		Validator::checkReturnUrl('https://example.com');

		try {
			Validator::checkReturnUrl('https://example.com/' . implode('-', array_fill(0, 100, 'long')));
			self::fail();

		} catch (InvalidArgumentException $e) {
			self::assertSame('ReturnUrl can have maximum of 300 characters.', $e->getMessage());
		}
	}

	public function testDescription(): void
	{
		Validator::checkDescription('foo description');

		try {
			Validator::checkDescription(implode(' ', array_fill(0, 60, 'very')) . ' long description');
			self::fail();

		} catch (InvalidArgumentException $e) {
			self::assertSame('Description can have maximum of 255 characters.', $e->getMessage());
		}
	}

	public function testMerchantData(): void
	{
		Validator::checkMerchantData('foo merchant data');

		try {
			Validator::checkMerchantData(implode(' ', array_fill(0, 60, 'very')) . ' long merchantData');
			self::fail();

		} catch (InvalidArgumentException $e) {
			self::assertSame('MerchantData can have maximum of 255 characters in encoded state.', $e->getMessage());
		}
	}

	public function testCustomerId(): void
	{
		Validator::checkCustomerId('foo customerId');

		try {
			Validator::checkCustomerId('very very very very very very very very long long long customerId');
			self::fail();

		} catch (InvalidArgumentException $e) {
			self::assertSame('CustomerId can have maximum of 50 characters.', $e->getMessage());
		}
	}

	public function testPayId(): void
	{
		Validator::checkPayId('foo payId');

		try {
			Validator::checkPayId('very long long payId');
			self::fail();

		} catch (InvalidArgumentException $e) {
			self::assertSame('PayId can have maximum of 15 characters.', $e->getMessage());
		}
	}

	public function testTtlSec(): void
	{
		Validator::checkTtlSec(500);

		try {
			Validator::checkTtlSec(200);
			self::fail();

		} catch (InvalidArgumentException $e) {
			self::assertSame('TTL sec is out of range (300 - 1800). Current value is 200.', $e->getMessage());
		}

		try {
			Validator::checkTtlSec(3000);
			self::fail();

		} catch (InvalidArgumentException $e) {
			self::assertSame('TTL sec is out of range (300 - 1800). Current value is 3000.', $e->getMessage());
		}
	}

}
