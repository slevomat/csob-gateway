<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class PaymentResponseTest extends TestCase
{

	public function testMerchantData(): void
	{
		$paymentResponse = new PaymentResponse(
			'123',
			new DateTimeImmutable(),
			ResultCode::get(ResultCode::C0_OK),
			'foo message',
			null,
			null,
			'merchant data',
		);

		self::assertSame('merchant data', $paymentResponse->getMerchantData());
	}

}
