<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;

class PaymentResponseTest extends \PHPUnit_Framework_TestCase
{

	public function testMerchantData()
	{
		$paymentResponse = new PaymentResponse(
			'123',
			new DateTimeImmutable(),
			ResultCode::get(ResultCode::C0_OK),
			'foo message',
			null,
			null,
			'merchant data'
		);

		$this->assertSame('merchant data', $paymentResponse->getMerchantData());
	}

}
