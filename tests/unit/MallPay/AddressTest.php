<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{

	public function testEncode(): void
	{
		$address = new Address('Slevomat', Country::get(Country::CZE), 'Praha 8', 'Pernerova 691/42', 'xxx', '186 00', AddressType::get(AddressType::BILLING));

		$expected = [
			'country' => 'CZ',
			'city' => 'Praha 8',
			'streetAddress' => 'Pernerova 691/42',
			'zip' => '186 00',
			'addressType' => 'BILLING',
			'name' => 'Slevomat',
			'streetNumber' => 'xxx',
		];

		self::assertSame($expected, $address->encode());
	}

	public function testValidation(): void
	{
		try {
			new Address('Slevomat', Country::get(Country::CZE), 'Praha 888 Praha 888 Praha 888 Praha 888 Praha 888 Praha 888', 'Pernerova 691/42', 'xxx', '186 00', AddressType::get(AddressType::BILLING));
			self::fail();
		} catch (InvalidArgumentException $e) {
			self::assertSame('Field must have maximum of 50 characters.', $e->getMessage());
		}
	}

}
