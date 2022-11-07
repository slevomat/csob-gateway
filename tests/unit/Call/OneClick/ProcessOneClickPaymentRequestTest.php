<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\OneClick;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\AdditionalData\Fingerprint;
use SlevomatCsobGateway\AdditionalData\FingerprintSdk;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;

class ProcessOneClickPaymentRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('oneclick/process', [
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
					'payId' => '123456789',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 2,
					'actions' => [
						'authenticate' => [
							'sdkChallenge' => [
								'threeDSServerTransID' => 'eeddda80-6ca7-4b22-9d6a-eb8e84791ec9',
								'acsReferenceNumber' => '3DS_LOA_ACS_201_13579',
								'acsTransID' => '7f3296a8-08c4-4afb-a3e2-8ce31b2e9069',
								'acsSignedContent' => 'base64-encoded-acs-signed-content',
							],
						],
					],
				]),
			);

		$request = new ProcessOneClickPaymentRequest(
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

		self::assertSame('123456789', $response->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $response->getResponseDateTime());
		self::assertSame(ResultCode::C0_OK, $response->getResultCode());
		self::assertSame('OK', $response->getResultMessage());
		self::assertSame(PaymentStatus::S2_IN_PROGRESS, $response->getPaymentStatus());
		self::assertNull($response->getActions()?->getFingerprint());
		self::assertSame('eeddda80-6ca7-4b22-9d6a-eb8e84791ec9', $response->getActions()?->getAuthenticate()?->getSdkChallenge()?->getThreeDSServerTransID());
		self::assertSame('3DS_LOA_ACS_201_13579', $response->getActions()->getAuthenticate()->getSdkChallenge()->getAcsReferenceNumber());
		self::assertSame('7f3296a8-08c4-4afb-a3e2-8ce31b2e9069', $response->getActions()->getAuthenticate()->getSdkChallenge()->getAcsTransID());
		self::assertSame('base64-encoded-acs-signed-content', $response->getActions()->getAuthenticate()->getSdkChallenge()->getAcsSignedContent());
	}

}
