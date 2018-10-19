<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Masterpass;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Call\PaymentResponse;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;

class StandardFinishRequestTest extends \PHPUnit\Framework\TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('masterpass/standard/finish', [
				'merchantId' => '012345',
				'payId' => '123456789',
				'oauthToken' => '123456789123456789',
				'totalAmount' => 15000,
			])
			->willReturn(
				new Response(ResponseCode::get(ResponseCode::S200_OK), [
					'payId' => '123456789',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 2,
				])
			);

		/** @var ApiClient $apiClient */
		$paymentRequest = new StandardFinishRequest(
			'012345',
			'123456789',
			'123456789123456789',
			15000
		);

		$checkoutResponse = $paymentRequest->send($apiClient);

		self::assertInstanceOf(PaymentResponse::class, $checkoutResponse);
		self::assertSame('123456789', $checkoutResponse->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $checkoutResponse->getResponseDateTime());
		self::assertEquals(ResultCode::get(ResultCode::C0_OK), $checkoutResponse->getResultCode());
		self::assertSame('OK', $checkoutResponse->getResultMessage());
		self::assertEquals(PaymentStatus::get(PaymentStatus::S2_IN_PROGRESS), $checkoutResponse->getPaymentStatus());
	}

}
