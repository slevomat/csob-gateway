<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Currency;

class OrderTest extends TestCase
{

	public function testEncode(): void
	{
		$order = new Order(
			Currency::get(Currency::EUR),
			OrderDeliveryType::get(OrderDeliveryType::DELIVERY_CARRIER),
			OrderCarrierId::get(OrderCarrierId::TNT),
			null
		);
		$order->addItem(
			'123',
			'345',
			'Super věc',
			OrderItemType::get(OrderItemType::PHYSICAL),
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
			'https://obchod.cz/produkt/123-345'
		);
		$order->addAddress('Slevomat', Country::get(Country::CZE), 'Praha 8', 'Pernerova 691/42', 'xxx', '186 00', AddressType::get(AddressType::BILLING));

		$expected = [
			'totalPrice' => [
				'amount' => 200,
				'currency' => 'EUR',
			],
			'totalVat' => [
				[
					'amount' => 40,
					'currency' => 'EUR',
					'vatRate' => 20,
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
			],
			'deliveryType' => 'DELIVERY_CARRIER',
			'carrierId' => 'TNT',
		];

		self::assertSame($expected, $order->encode());
	}

}
