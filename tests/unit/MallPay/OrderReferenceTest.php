<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Currency;
use SlevomatCsobGateway\Price;

class OrderReferenceTest extends TestCase
{

	public function testEncode(): void
	{
		$orderReference = new OrderReference(
			new Price(200, Currency::get(Currency::EUR)),
			[
				new Vat(40, Currency::get(Currency::EUR), 20),
			]
		);
		$orderReference->addItem('123', '345', 'Super věc', OrderItemType::get(OrderItemType::PHYSICAL), 2);

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
			'items' => [
				[
					'code' => '123',
					'name' => 'Super věc',
					'ean' => '345',
					'type' => 'PHYSICAL',
					'quantity' => 2,
				],
			],
		];

		self::assertSame($expected, $orderReference->encode());
	}

}
