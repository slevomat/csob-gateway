<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Extension;

use PHPUnit\Framework\TestCase;

class MaskedCardNumberExtensionTest extends TestCase
{

	public function testCreateResponse(): void
	{
		$maskedCardNumberExtension = new MaskedCardNumberExtension();
		$maskedCardNumberResponse = $maskedCardNumberExtension->createResponse([
			'longMaskedCln' => '415461****0209',
			'maskedCln' => '****0209',
			'expiration' => '11/16',
		]);

		self::assertSame('415461****0209', $maskedCardNumberResponse->getLongMaskedCln());
		self::assertSame('****0209', $maskedCardNumberResponse->getMaskedCln());
		self::assertSame('2016-11-30', $maskedCardNumberResponse->getExpiration()->format('Y-m-d'));
	}

}
