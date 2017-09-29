<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Extension;

class TransactionSettlementExtensionTest extends \PHPUnit\Framework\TestCase
{

	public function testCreateResponse(): void
	{
		$transactionSettlementExtension = new TransactionSettlementExtension();
		$transactionSettlementResponse = $transactionSettlementExtension->createResponse([
			'createdDate' => '2016-04-12T12:06:20.848Z',
			'authDate' => '160412100635',
			'settlementDate' => '20160412',
		]);

		$this->assertSame('2016-04-12 12:06:20 848000', $transactionSettlementResponse->getCreatedDate()->format('Y-m-d H:i:s u'));
		/** @var \DateTimeImmutable $authDate */
		$authDate = $transactionSettlementResponse->getAuthDate();
		$this->assertNotNull($authDate);
		$this->assertSame('2016-04-12 10:06:35', $authDate->format('Y-m-d H:i:s'));
		/** @var \DateTimeImmutable $settlementDate */
		$settlementDate = $transactionSettlementResponse->getSettlementDate();
		$this->assertNotNull($settlementDate);
		$this->assertSame('2016-04-12', $settlementDate->format('Y-m-d'));
	}

}
