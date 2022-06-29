<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Country;

class AddressTest extends TestCase
{

	public function testEncode(): void
	{
		$address = new Address('Slevomat', Country::CZE, 'Praha 8', 'Pernerova 691/42', 'xxx', '186 00', AddressType::BILLING);

		$expected = [
			'name' => 'Slevomat',
			'country' => 'CZ',
			'city' => 'Praha 8',
			'streetAddress' => 'Pernerova 691/42',
			'streetNumber' => 'xxx',
			'zip' => '186 00',
			'addressType' => 'BILLING',
		];

		self::assertSame($expected, $address->encode());
	}

	public function testEncodeForSignature(): void
	{
		$expected = [
			'name' => null,
			'country' => null,
			'city' => null,
			'streetAddress' => null,
			'streetNumber' => null,
			'zip' => null,
			'addressType' => null,
		];

		self::assertSame($expected, Address::encodeForSignature());
	}

	public function testValidation(): void
	{
		try {
			new Address('Slevomat', Country::CZE, 'Praha 888 Praha 888 Praha 888 Praha 888 Praha 888 Praha 888', 'Pernerova 691/42', 'xxx', '186 00', AddressType::BILLING);
			self::fail();
		} catch (InvalidArgumentException $e) {
			self::assertSame('Field must have maximum of 50 characters.', $e->getMessage());
		}
	}

}
