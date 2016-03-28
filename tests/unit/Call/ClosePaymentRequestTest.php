<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class ClosePaymentRequestTest extends \PHPUnit_Framework_TestCase
{

	public function testSend()
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('put')
			->with('payment/close', [
				'merchantId' => '012345',
				'payId' => '123456789',
			])
			->willReturn(
				new Response(new ResponseCode(ResponseCode::S200_OK), [
					'payId' => '123456789',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 7,
				])
			);

		/** @var ApiClient $apiClient */
		$paymentRequest = new ClosePaymentRequest(
			'012345',
			'123456789'
		);

		$closePaymentResponse = $paymentRequest->send($apiClient);

		$this->assertInstanceOf(PaymentResponse::class, $closePaymentResponse);
		$this->assertSame('123456789', $closePaymentResponse->getPayId());
		$this->assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $closePaymentResponse->getResponseDateTime());
		$this->assertEquals(new ResultCode(ResultCode::C0_OK), $closePaymentResponse->getResultCode());
		$this->assertSame('OK', $closePaymentResponse->getResultMessage());
		$this->assertEquals(new PaymentStatus(PaymentStatus::S7_AWAITING_SETTLEMENT), $closePaymentResponse->getPaymentStatus());
		$this->assertNull($closePaymentResponse->getAuthCode());
	}

}
