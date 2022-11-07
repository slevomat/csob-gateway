<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\GooglePay;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Country;

class EchoGooglePayRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('googlepay/echo', [
				'merchantId' => '012345',
			])
			->willReturn(
				new Response(ResponseCode::S200_OK, [
					'dttm' => '20190425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'initParams' => [
						'apiVersion' => 2,
						'apiVersionMinor' => 0,
						'paymentMethodType' => 'CARD',
						'allowedCardNetworks' => ['VISA', 'MASTERCARD'],
						'allowedCardAuthMethods' => ['CRYPTOGRAM_3DS'],
						'assuranceDetailsRequired' => true,
						'billingAddressRequired' => true,
						'billingAddressParametersFormat' => 'FULL',
						'tokenizationSpecificationType' => 'PAYMENT_GATEWAY',
						'gateway' => 'csob',
						'gatewayMerchantId' => 'M1MIPS0000',
						'googlepayMerchantId' => '01234567890123456789',
						'merchantName' => 'Váš obchod, a.s.',
						'environment' => 'TEST',
						'totalPriceStatus' => 'FINAL',
						'countryCode' => 'CZ',
					],
				]),
			);

		$request = new EchoGooglePayRequest('012345');

		$response = $request->send($apiClient);

		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20190425131559'), $response->getResponseDateTime());
		self::assertSame(ResultCode::C0_OK, $response->getResultCode());
		self::assertSame('OK', $response->getResultMessage());
		self::assertSame(['VISA', 'MASTERCARD'], $response->getInitParams()?->getAllowedCardNetworks());
		self::assertSame(Country::CZE, $response->getInitParams()->getCountryCode());
		self::assertSame(InitParamsEnvironment::TEST, $response->getInitParams()->getEnvironment());
		self::assertSame(0, $response->getInitParams()->getApiVersionMinor());
	}

}
