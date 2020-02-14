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

class StandardCheckoutRequestTest extends TestCase
{

	public function testSend(): void
	{
		/** @var ApiClient|MockObject $apiClient */
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('masterpass/standard/checkout', [
				'merchantId' => '012345',
				'payId' => '123456789',
				'callbackUrl' => 'https://www.vasobchod.cz/masterpass/callback',
				'shippingLocationProfile' => 'SP-0001',
			])
			->willReturn(
				new Response(ResponseCode::get(ResponseCode::S200_OK), [
					'payId' => '123456789',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 1,
					'lightboxParams' => [
						'requestToken' => '6a79bf9e320a0460d08aee7ad154f7dab4e19503',
						'callbackUrl' => 'https://www.vasobchod.cz/masterpass/callback',
						'merchantCheckoutId' => 'a4a6w4vzajswviqy5oeu11irc2e3yb51ws',
						'allowedCardTypes' => 'master,visa',
						'suppressShippingAddressEnable' => 'true',
						'loyaltyEnabled' => 'false',
						'version' => 'v6',
						'shippingLocationProfile' => 'SP-0001',
					],
				])
			);

		$paymentRequest = new StandardCheckoutRequest(
			'012345',
			'123456789',
			'https://www.vasobchod.cz/masterpass/callback',
			'SP-0001'
		);

		$checkoutResponse = $paymentRequest->send($apiClient);

		self::assertSame('123456789', $checkoutResponse->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $checkoutResponse->getResponseDateTime());
		self::assertEquals(ResultCode::get(ResultCode::C0_OK), $checkoutResponse->getResultCode());
		self::assertSame('OK', $checkoutResponse->getResultMessage());
		self::assertEquals(PaymentStatus::get(PaymentStatus::S1_CREATED), $checkoutResponse->getPaymentStatus());
		self::assertNotNull($checkoutResponse->getLightboxParams());
	}

}
