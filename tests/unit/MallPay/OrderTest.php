<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Currency;

class OrderTest extends TestCase
{

	public function testEncode(): void
	{
		$order = new Order(
			Currency::EUR,
			OrderDeliveryType::DELIVERY_CARRIER,
			OrderCarrierId::TNT,
			null,
		);
		$order->addItem(
			'123',
			'345',
			'Super věc',
			OrderItemType::PHYSICAL,
			2,
			'Varianta 1',
			'Popisek',
			'Producer',
			['kategorie 1', 'kategorie 2'],
			100,
			200,
			20,
			40,
			20,
			'https://obchod.cz/produkt/123-345',
		);
		$order->addItem(
			'discount',
			null,
			'Sleva',
			OrderItemType::DISCOUNT,
			2,
			null,
			null,
			null,
			null,
			-50,
			-100,
			null,
			0,
			0,
			null,
		);
		$order->addAddress('Slevomat', Country::CZE, 'Praha 8', 'Pernerova 691/42', 'xxx', '186 00', AddressType::BILLING);

		$expected = [
			'totalPrice' => [
				'amount' => 100,
				'currency' => 'EUR',
			],
			'totalVat' => [
				[
					'amount' => 40,
					'currency' => 'EUR',
					'vatRate' => 20,
				],
				[
					'amount' => 0,
					'currency' => 'EUR',
					'vatRate' => 0,
				],
			],
			'addresses' => [
				[
					'country' => 'CZ',
					'city' => 'Praha 8',
					'streetAddress' => 'Pernerova 691/42',
					'zip' => '186 00',
					'addressType' => 'BILLING',
					'name' => 'Slevomat',
					'streetNumber' => 'xxx',
				],
			],
			'items' => [
				[
					'code' => '123',
					'name' => 'Super věc',
					'totalPrice' => [
						'amount' => 200,
						'currency' => 'EUR',
					],
					'totalVat' => [
						'amount' => 40,
						'currency' => 'EUR',
						'vatRate' => 20,
					],
					'ean' => '345',
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
					'productUrl' => 'https://obchod.cz/produkt/123-345',
				],
				[
					'code' => 'discount',
					'name' => 'Sleva',
					'totalPrice' => [
						'amount' => -100,
						'currency' => 'EUR',
					],
					'totalVat' => [
						'amount' => 0,
						'currency' => 'EUR',
						'vatRate' => 0,
					],
					'type' => 'DISCOUNT',
					'quantity' => 2,
					'unitPrice' => [
						'amount' => -50,
						'currency' => 'EUR',
					],
				],
			],
			'deliveryType' => 'DELIVERY_CARRIER',
			'carrierId' => 'TNT',
		];

		self::assertSame($expected, $order->encode());
	}

}
