<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class OrderItemReferenceTest extends TestCase
{

	public function testEncode(): void
	{
		$orderItemReference = new OrderItemReference(
			'123',
			'345',
			'Super věc',
			OrderItemType::PHYSICAL,
			2,
		);

		$expected = [
			'code' => '123',
			'ean' => '345',
			'name' => 'Super věc',
			'type' => 'PHYSICAL',
			'quantity' => 2,
		];

		self::assertSame($expected, $orderItemReference->encode());
	}

	public function testEncodeForSignature(): void
	{
		$expected = [
			'totalPrice' => [
				'amount' => null,
				'currency' => null,
			],
			'totalVat' => [
				[
					'amount' => null,
					'currency' => null,
					'vatRate' => null,
				],
			],
			'items' => [
				[
					'code' => null,
					'ean' => null,
					'name' => null,
					'type' => null,
					'quantity' => null,
				],
			],
		];

		self::assertSame($expected, OrderReference::encodeForSignature());
	}

	public function testValidation(): void
	{
		try {
			new OrderItemReference(
				'123 123 123 123 123 123 123 123 123 123 123 123 123 123',
				'345',
				'Super věc',
				OrderItemType::PHYSICAL,
				2,
			);

			self::fail();
		} catch (InvalidArgumentException $e) {
			self::assertSame('Field must have maximum of 50 characters.', $e->getMessage());
		}
		try {
			new OrderItemReference(
				'123',
				'345',
				'Super věc',
				OrderItemType::PHYSICAL,
				-2,
			);
			self::fail();
		} catch (InvalidArgumentException $e) {
			self::assertSame('Value is negative or zero.', $e->getMessage());
		}
	}

}
