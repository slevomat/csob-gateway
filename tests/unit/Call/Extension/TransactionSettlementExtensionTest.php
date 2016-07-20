<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Extension;

class TransactionSettlementExtensionTest extends \PHPUnit_Framework_TestCase
{

	public function testCreateResponse()
	{
		$transactionSettlementExtension = new TransactionSettlementExtension();
		$transactionSettlementResponse = $transactionSettlementExtension->createResponse([
			'createdDate' => '2016-04-12T12:06:20.848Z',
			'authDate' => '160412100635',
			'settlementDate' => '20160412',
		]);

		$this->assertSame('2016-04-12 12:06:20 848000', $transactionSettlementResponse->getCreatedDate()->format('Y-m-d H:i:s u'));
		$this->assertSame('2016-04-12 10:06:35', $transactionSettlementResponse->getAuthDate()->format('Y-m-d H:i:s'));
		$this->assertSame('2016-04-12', $transactionSettlementResponse->getSettlementDate()->format('Y-m-d'));
	}

}
