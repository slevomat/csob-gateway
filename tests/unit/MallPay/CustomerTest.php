<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{

	public function testNames(): void
	{
		$customer = new Customer('Pepa', 'Zdepa', null, null, null, 'pepa@zdepa.cz', '+420800300300', null, null);

		self::assertNull($customer->getFullName());
		self::assertSame('Pepa', $customer->getFirstName());
		self::assertSame('Zdepa', $customer->getLastName());

		$customer2 = new Customer(null, null, 'Pepa Zdepa', null, null, 'pepa@zdepa.cz', '+420800300300', null, null);

		self::assertSame('Pepa Zdepa', $customer2->getFullName());
		self::assertNull($customer2->getFirstName());
		self::assertNull($customer2->getLastName());
	}

	public function testEncode(): void
	{
		$customer = new Customer(null, null, 'Pepa Zdepa', 'Ing', 'Ph.d', 'pepa@zdepa.cz', '+420800300300', '123', '345');

		$expected = [
			'fullName' => 'Pepa Zdepa',
			'titleBefore' => 'Ing',
			'titleAfter' => 'Ph.d',
			'email' => 'pepa@zdepa.cz',
			'phone' => '+420800300300',
			'tin' => '123',
			'vatin' => '345',
		];

		self::assertSame($expected, $customer->encode());
	}

	public function testEncodeForSignature(): void
	{
		$expected = [
			'firstName' => null,
			'lastName' => null,
			'fullName' => null,
			'titleBefore' => null,
			'titleAfter' => null,
			'email' => null,
			'phone' => null,
			'tin' => null,
			'vatin' => null,
		];

		self::assertSame($expected, Customer::encodeForSignature());
	}

	public function testValidation(): void
	{
		try {
			new Customer(null, 'Zdepa', null, null, null, 'pepa@zdepa.cz', '+420800300300', null, null);
			self::fail();
		} catch (InvalidArgumentException $e) {
			self::assertSame('Either fullName or (firstName and lastName) must be set.', $e->getMessage());
		}
		try {
			new Customer(null, null, null, null, null, 'pepa@zdepa.cz', '+420800300300', null, null);
			self::fail();
		} catch (InvalidArgumentException $e) {
			self::assertSame('Either fullName or (firstName and lastName) must be set.', $e->getMessage());
		}
		try {
			new Customer('Pepa', 'Zdepa', null, null, null, 'ne email', '+420800300300', null, null);
			self::fail();
		} catch (InvalidArgumentException $e) {
			self::assertSame('E-mail is not valid.', $e->getMessage());
		}
		try {
			new Customer('Pepa', 'Zdepa', null, 'Toooooo loooooong tiiiiiitle', null, 'pepa@zdepa.cz', '+420800300300', null, null);
			self::fail();
		} catch (InvalidArgumentException $e) {
			self::assertSame('Field must have maximum of 20 characters.', $e->getMessage());
		}
	}

}
