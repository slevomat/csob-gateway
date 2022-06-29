<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\MallPay;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\MallPay\OrderItemReference;
use SlevomatCsobGateway\MallPay\OrderItemType;

class RefundMallPayRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('put')
			->with('mallpay/refund', [
				'merchantId' => '012345',
				'payId' => '12345',
				'amount' => 10000,
				'refundedItems' => [
					[
						'code' => '123',
						'name' => 'Super věc',
						'ean' => '345',
						'type' => 'PHYSICAL',
						'quantity' => 2,
					],
				],
			])
			->willReturn(
				new Response(ResponseCode::S200_OK, [
					'payId' => '123456789',
					'dttm' => '20210505092159',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 1,
					'mallpayUrl' => 'https://mallpay.cz',
				]),
			);

		$request = new RefundMallPayRequest(
			'012345',
			'12345',
			10000,
			[
				new OrderItemReference('123', '345', 'Super věc', OrderItemType::PHYSICAL, 2),
			],
		);

		$response = $request->send($apiClient);

		self::assertSame('123456789', $response->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20210505092159'), $response->getResponseDateTime());
		self::assertSame(ResultCode::C0_OK, $response->getResultCode());
		self::assertSame('OK', $response->getResultMessage());
		self::assertSame(PaymentStatus::S1_CREATED, $response->getPaymentStatus());
	}

}
