<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\ApplePay;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\AdditionalData\Fingerprint;
use SlevomatCsobGateway\AdditionalData\FingerprintBrowser;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;

class ProcessApplePayRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('applepay/process', [
				'merchantId' => '012345',
				'payId' => 'ef08b6e9f22345c',
				'fingerprint' => [
					'browser' => [
						'userAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.99 Safari/537.36',
						'acceptHeader' => 'text/html,application/xhtml+xml,application/xml;',
						'language' => 'en',
						'javascriptEnabled' => false,
					],
				],
			])
			->willReturn(
				new Response(ResponseCode::S200_OK, [
					'payId' => 'ef08b6e9f22345c',
					'dttm' => '20190425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 2,
					'actions' => [
						'authenticate' => [
							'browserChallenge' => [
								'url' => 'https://example.com/challenge-endpoint',
							],
						],
					],
				]),
			);

		$request = new ProcessApplePayRequest(
			'012345',
			'ef08b6e9f22345c',
			new Fingerprint(
				new FingerprintBrowser(
					'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.99 Safari/537.36',
					'text/html,application/xhtml+xml,application/xml;',
					'en',
					false,
					null,
					null,
					null,
					null,
					null,
					null,
				),
			),
		);

		$response = $request->send($apiClient);

		self::assertSame('ef08b6e9f22345c', $response->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20190425131559'), $response->getResponseDateTime());
		self::assertSame(ResultCode::C0_OK, $response->getResultCode());
		self::assertSame('OK', $response->getResultMessage());
		self::assertSame(PaymentStatus::S2_IN_PROGRESS, $response->getPaymentStatus());
		self::assertNull($response->getStatusDetail());
		self::assertSame('https://example.com/challenge-endpoint', $response->getActions()?->getAuthenticate()?->getBrowserChallenge()?->getUrl());
	}

}
