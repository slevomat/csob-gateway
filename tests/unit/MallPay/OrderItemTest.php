<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Currency;
use SlevomatCsobGateway\Price;

class OrderItemTest extends TestCase
{

	public function testEncode(): void
	{
		$orderItem = new OrderItem(
			'123',
			'345',
			'Super věc',
			OrderItemType::PHYSICAL,
			2,
			'Varianta 1',
			'Popisek',
			'Producer',
			['kategorie 1', 'kategorie 2'],
			new Price(100, Currency::EUR),
			new Vat(20, Currency::EUR, 20),
			new Price(200, Currency::EUR),
			new Vat(40, Currency::EUR, 20),
			'https://obchod.cz/produkt/123-345',
		);

		$expected = [
			'code' => '123',
			'ean' => '345',
			'name' => 'Super věc',
			'type' => 'PHYSICAL',
			'quantity' => 2,
			'variant' => 'Varianta 1',
			'description' => 'Popisek',
			'producer' => 'Producer',
			'categories' => ['kategorie 1', 'kategorie 2'],
			'unitPrice' => [
				'amount' => 100,
				'currency' => 'EUR',
			],
			'unitVat' => [
				'amount' => 20,
				'currency' => 'EUR',
				'vatRate' => 20,
			],
			'totalPrice' => [
				'amount' => 200,
				'currency' => 'EUR',
			],
			'totalVat' => [
				'amount' => 40,
				'currency' => 'EUR',
				'vatRate' => 20,
			],
			'productUrl' => 'https://obchod.cz/produkt/123-345',
		];

		self::assertSame($expected, $orderItem->encode());
	}

	public function testEncodeForSignature(): void
	{
		$expected = [
			'code' => null,
			'ean' => null,
			'name' => null,
			'type' => null,
			'quantity' => null,
			'variant' => null,
			'description' => null,
			'producer' => null,
			'categories' => [],
			'unitPrice' => [
				'amount' => null,
				'currency' => null,
			],
			'unitVat' => [
				'amount' => null,
				'currency' => null,
				'vatRate' => null,
			],
			'totalPrice' => [
				'amount' => null,
				'currency' => null,
			],
			'totalVat' => [
				'amount' => null,
				'currency' => null,
				'vatRate' => null,
			],
			'productUrl' => null,
		];

		self::assertSame($expected, OrderItem::encodeForSignature());
	}

	public function testValidation(): void
	{
		try {
			new OrderItem(
				'123 123 123 123 123 123 123 123 123 123 123 123 123 123',
				'345',
				'Super věc',
				OrderItemType::PHYSICAL,
				2,
				'Varianta 1',
				'Popisek',
				'Producer',
				['kategorie 1', 'kategorie 2'],
				new Price(100, Currency::EUR),
				new Vat(20, Currency::EUR, 20),
				new Price(200, Currency::EUR),
				new Vat(40, Currency::EUR, 20),
				'https://obchod.cz/produkt/123-345',
			);

			self::fail();
		} catch (InvalidArgumentException $e) {
			self::assertSame('Field must have maximum of 50 characters.', $e->getMessage());
		}
		try {
			new OrderItem(
				'123',
				'345',
				'Super věc',
				OrderItemType::PHYSICAL,
				-2,
				'Varianta 1',
				'Popisek',
				'Producer',
				['kategorie 1', 'kategorie 2'],
				new Price(100, Currency::EUR),
				new Vat(20, Currency::EUR, 20),
				new Price(200, Currency::EUR),
				new Vat(40, Currency::EUR, 20),
				'https://obchod.cz/produkt/123-345',
			);
			self::fail();
		} catch (InvalidArgumentException $e) {
			self::assertSame('Value is negative or zero.', $e->getMessage());
		}
		try {
			new OrderItem(
				'123',
				'345',
				'Super věc',
				OrderItemType::PHYSICAL,
				2,
				'Varianta 1',
				'Popisek',
				'Producer',
				['kategorie 1', 'kategorie 2'],
				new Price(100, Currency::EUR),
				new Vat(20, Currency::EUR, 20),
				new Price(200, Currency::EUR),
				new Vat(40, Currency::EUR, 20),
				'https://obc hod.cz/produkt/123-345',
			);
			self::fail();
		} catch (InvalidArgumentException $e) {
			self::assertSame('URL is not valid.', $e->getMessage());
		}
	}

}
