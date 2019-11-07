<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Extension;

use PHPUnit\Framework\TestCase;

class TransactionSettlementExtensionTest extends TestCase
{

	public function testCreateResponse(): void
	{
		$transactionSettlementExtension = new TransactionSettlementExtension();
		$transactionSettlementResponse = $transactionSettlementExtension->createResponse([
			'createdDate' => '2016-04-12T12:06:20.848Z',
			'authDate' => '160412100635',
			'settlementDate' => '20160412',
		]);

		self::assertSame('2016-04-12 12:06:20 848000', $transactionSettlementResponse->getCreatedDate()->format('Y-m-d H:i:s u'));
		$authDate = $transactionSettlementResponse->getAuthDate();
		self::assertNotNull($authDate);
		self::assertSame('2016-04-12 10:06:35', $authDate->format('Y-m-d H:i:s'));
		$settlementDate = $transactionSettlementResponse->getSettlementDate();
		self::assertNotNull($settlementDate);
		self::assertSame('2016-04-12', $settlementDate->format('Y-m-d'));
	}

}
