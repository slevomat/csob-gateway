<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class ProcessPaymentRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('get')
			->with('payment/process/{merchantId}/{payId}/{dttm}/{signature}', [
				'merchantId' => '012345',
				'payId' => '123456789',
			])
			->willReturn(
				new Response(ResponseCode::get(ResponseCode::S200_OK), [], [
					'Location' => 'https://platebnibrana.csob.cz/pay/vasobchod.cz/6544-4564-sd65111-GF544DS/',
				])
			);

		/** @var ApiClient $apiClient */
		$processPaymentRequest = new ProcessPaymentRequest(
			'012345',
			'123456789'
		);

		$processPaymentResponse = $processPaymentRequest->send($apiClient);

		self::assertSame('https://platebnibrana.csob.cz/pay/vasobchod.cz/6544-4564-sd65111-GF544DS/', $processPaymentResponse->getGatewayLocationUrl());
	}

}
