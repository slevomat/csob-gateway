<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\MallPay;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\MallPay\CancelReason;

class CancelMallPayRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('put')
			->with('mallpay/cancel', [
				'merchantId' => '012345',
				'payId' => '12345',
				'reason' => CancelReason::ABANDONED,
			])
			->willReturn(
				new Response(ResponseCode::get(ResponseCode::S200_OK), [
					'payId' => '123456789',
					'dttm' => '20210505092159',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 1,
					'mallpayUrl' => 'https://mallpay.cz',
				]),
			);

		$request = new CancelMallPayRequest(
			'012345',
			'12345',
			CancelReason::get(CancelReason::ABANDONED),
		);

		$response = $request->send($apiClient);

		self::assertSame('123456789', $response->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20210505092159'), $response->getResponseDateTime());
		self::assertEquals(ResultCode::get(ResultCode::C0_OK), $response->getResultCode());
		self::assertSame('OK', $response->getResultMessage());
		self::assertEquals(PaymentStatus::get(PaymentStatus::S1_CREATED), $response->getPaymentStatus());
	}

}
