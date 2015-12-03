<?php

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class PaymentResponseTest extends \PHPUnit_Framework_TestCase
{

	public function testMerchantData()
	{
		$paymentResponse = new PaymentResponse(
			'123',
			new DateTimeImmutable(),
			new ResultCode(ResultCode::C0_OK),
			'foo message',
			null,
			null,
			'merchant data'
		);

		$this->assertSame('merchant data', $paymentResponse->getMerchantData());
	}

}
