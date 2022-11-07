<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class PaymentStatusRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('get')
			->with('payment/status/{merchantId}/{payId}/{dttm}/{signature}', [
				'merchantId' => '012345',
				'payId' => '123456789',
			])
			->willReturn(
				new Response(ResponseCode::S200_OK, [
					'payId' => '123456789',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 4,
					'authCode' => 'F7A23E',
					'actions' => [
						'fingerprint' => [
							'browserInit' => [
								'url' => 'https://example.com/3ds-method-endpoint',
							],
						],
						'authenticate' => [
							'browserChallenge' => [
								'url' => 'https://example.com/challenge-endpoint',
								'method' => 'GET',
							],
						],
					],
				]),
			);

		$paymentStatusRequest = new PaymentStatusRequest(
			'012345',
			'123456789',
		);

		$response = $paymentStatusRequest->send($apiClient);

		self::assertSame('123456789', $response->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $response->getResponseDateTime());
		self::assertSame(ResultCode::C0_OK, $response->getResultCode());
		self::assertSame('OK', $response->getResultMessage());
		self::assertSame(PaymentStatus::S4_CONFIRMED, $response->getPaymentStatus());
		self::assertSame('F7A23E', $response->getAuthCode());
		self::assertSame('https://example.com/3ds-method-endpoint', $response->getActions()?->getFingerprint()?->getBrowserInit()?->getUrl());
		self::assertSame('https://example.com/challenge-endpoint', $response->getActions()->getAuthenticate()?->getBrowserChallenge()?->getUrl());
		self::assertSame(HttpMethod::GET, $response->getActions()->getAuthenticate()->getBrowserChallenge()->getMethod());
	}

}
