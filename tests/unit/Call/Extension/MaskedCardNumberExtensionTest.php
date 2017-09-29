<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Extension;

class MaskedCardNumberExtensionTest extends \PHPUnit\Framework\TestCase
{

	public function testCreateResponse(): void
	{
		$maskedCardNumberExtension = new MaskedCardNumberExtension();
		$maskedCardNumberResponse = $maskedCardNumberExtension->createResponse([
			'longMaskedCln' => '415461****0209',
			'maskedCln' => '****0209',
			'expiration' => '11/16',
		]);

		$this->assertSame('415461****0209', $maskedCardNumberResponse->getLongMaskedCln());
		$this->assertSame('****0209', $maskedCardNumberResponse->getMaskedCln());
		$this->assertSame('2016-11-30', $maskedCardNumberResponse->getExpiration()->format('Y-m-d'));
	}

}
