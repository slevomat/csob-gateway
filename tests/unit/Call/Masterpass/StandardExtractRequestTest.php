<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Masterpass;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;

class StandardExtractRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('masterpass/standard/extract', [
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
					'checkoutParams' => ['card' => [
						'maskedCln' => '****4145',
						'expiration' => '11/19',
						'billingAddress' =>
								[
									'city' => 'Praha 1',
									'country' => 'CZ',
									'line1' => 'Jindřišská 16',
									'postalCode' => '11150',
								],
					],
						'shippingAddress' =>
							[
								'recipientName' => 'Jan Novák',
								'recipientPhoneNumber' => '+420602123456',
								'city' => 'Praha 1',
								'country' => 'CZ',
								'line1' => 'Dlouhá 23',
								'postalCode' => '11150',
							],
					],
				]),
			);

		$paymentRequest = new StandardExtractRequest(
			'012345',
			'123456789',
			[
				'mpstatus' => 'success',
				'oauthToken' => '6a79bf9e320a0460d08aee7ad154f7dab4e19503',
				'checkoutResourceUrl' => 'https://sandbox.api.mastercard.com/masterpass/v6/checkout/616764812',
				'oauthVerifier' => 'fc8f41bb76ed7d43ea6d714cb8fdefa606611a7d',
			],
		);

		$extractResponse = $paymentRequest->send($apiClient);

		self::assertSame('123456789', $extractResponse->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $extractResponse->getResponseDateTime());
		self::assertEquals(ResultCode::get(ResultCode::C0_OK), $extractResponse->getResultCode());
		self::assertSame('OK', $extractResponse->getResultMessage());
		self::assertEquals(PaymentStatus::get(PaymentStatus::S2_IN_PROGRESS), $extractResponse->getPaymentStatus());
		self::assertNotNull($extractResponse->getCheckoutParams());
	}

}
