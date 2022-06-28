<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\ApplePay;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Country;

class EchoApplePayRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('applepay/echo', [
				'merchantId' => '012345',
			])
			->willReturn(
				new Response(ResponseCode::S200_OK, [
					'dttm' => '20190425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'initParams' => [
						'countryCode' => 'CZ',
						'supportedNetworks' => ['visa', 'masterCard', 'maestro'],
						'merchantCapabilities' => ['supports3DS'],
					],
				]),
			);

		$request = new EchoApplePayRequest('012345');

		$response = $request->send($apiClient);

		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20190425131559'), $response->getResponseDateTime());
		self::assertSame(ResultCode::C0_OK, $response->getResultCode());
		self::assertSame('OK', $response->getResultMessage());
		self::assertSame(Country::CZE, $response->getInitParams()?->getCountryCode());
		self::assertSame(['visa', 'masterCard', 'maestro'], $response->getInitParams()?->getSupportedNetworks());
		self::assertSame(['supports3DS'], $response->getInitParams()?->getMerchantCapabilities());
	}

}
