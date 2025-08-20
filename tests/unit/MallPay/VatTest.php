<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Currency;

class VatTest extends TestCase
{

	public function testEncode(): void
	{
		$vat = new Vat(123, Currency::USD, 15);

		self::assertSame(['amount' => 123, 'currency' => 'USD', 'vatRate' => 15], $vat->encode());
	}

}
