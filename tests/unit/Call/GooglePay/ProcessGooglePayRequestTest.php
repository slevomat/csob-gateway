<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\GooglePay;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\AdditionalData\Fingerprint;
use SlevomatCsobGateway\AdditionalData\FingerprintSdk;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;

class ProcessGooglePayRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('googlepay/process', [
				'merchantId' => '012345',
				'payId' => 'ef08b6e9f22345c',
				'fingerprint' => [
					'sdk' => [
						'appID' => '198d0791-0025-4183-b9ae-900c88dd80e0',
						'encData' => 'encrypted-data',
						'ephemPubKey' => 'encoded-public-key',
						'maxTimeout' => 5,
						'referenceNumber' => 'sdk-reference-number',
						'transID' => '7f101033-df46-4f5c-9e96-9575c924e1e7',
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
					'statusDetail' => 'detail',
				]),
			);

		$request = new ProcessGooglePayRequest(
			'012345',
			'ef08b6e9f22345c',
			new Fingerprint(
				null,
				new FingerprintSdk(
					'198d0791-0025-4183-b9ae-900c88dd80e0',
					'encrypted-data',
					'encoded-public-key',
					5,
					'sdk-reference-number',
					'7f101033-df46-4f5c-9e96-9575c924e1e7',
				),
			),
		);

		$response = $request->send($apiClient);

		self::assertSame('ef08b6e9f22345c', $response->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20190425131559'), $response->getResponseDateTime());
		self::assertSame(ResultCode::C0_OK, $response->getResultCode());
		self::assertSame('OK', $response->getResultMessage());
		self::assertSame(PaymentStatus::S2_IN_PROGRESS, $response->getPaymentStatus());
		self::assertSame('detail', $response->getStatusDetail());
		self::assertNull($response->getActions());
	}

}
