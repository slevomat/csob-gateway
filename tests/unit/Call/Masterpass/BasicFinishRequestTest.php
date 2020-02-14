<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Masterpass;

use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;

class BasicFinishRequestTest extends TestCase
{

	public function testSend(): void
	{
		/** @var ApiClient|MockObject $apiClient */
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('masterpass/basic/finish', [
				'merchantId' => '012345',
				'payId' => '123456789',
				'callbackParams' => [
					'mpstatus' => 'success',
					'oauthToken' => '6a79bf9e320a0460d08aee7ad154f7dab4e19503',
					'checkoutResourceUrl' => 'https://sandbox.api.mastercard.com/masterpass/v6/checkout/616764812',
					'oauthVerifier' => 'fc8f41bb76ed7d43ea6d714cb8fdefa606611a7d',
				],
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

		$paymentRequest = new BasicFinishRequest(
			'012345',
			'123456789',
			[
				'mpstatus' => 'success',
				'oauthToken' => '6a79bf9e320a0460d08aee7ad154f7dab4e19503',
				'checkoutResourceUrl' => 'https://sandbox.api.mastercard.com/masterpass/v6/checkout/616764812',
				'oauthVerifier' => 'fc8f41bb76ed7d43ea6d714cb8fdefa606611a7d',
			]
		);

		$checkoutResponse = $paymentRequest->send($apiClient);

		self::assertSame('123456789', $checkoutResponse->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $checkoutResponse->getResponseDateTime());
		self::assertEquals(ResultCode::get(ResultCode::C0_OK), $checkoutResponse->getResultCode());
		self::assertSame('OK', $checkoutResponse->getResultMessage());
		self::assertEquals(PaymentStatus::get(PaymentStatus::S2_IN_PROGRESS), $checkoutResponse->getPaymentStatus());
	}

}
