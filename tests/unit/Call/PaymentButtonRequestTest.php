<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class PaymentButtonRequestTest extends \PHPUnit\Framework\TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('payment/button', [
				'merchantId' => '012345',
				'payId' => '123456789',
				'brand' => 'csob',
			])
			->willReturn(
				new Response(ResponseCode::get(ResponseCode::S200_OK), [
					'payId' => '123456789',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 1,
					'redirect' => [
						'method' => 'GET',
						'url' => 'https://platebnibrana.csob.cz/pay/vasobchod.cz/2c72d818-9788-45a1-878a-9db2a706edc5/pt-detect/csob',
					],
				])
			);

		/** @var ApiClient $apiClient */
		$paymentRequest = new PaymentButtonRequest(
			'012345',
			'123456789',
			PaymentButtonBrand::get(PaymentButtonBrand::CSOB)
		);

		$paymentButtonResponse = $paymentRequest->send($apiClient);

		$this->assertInstanceOf(PaymentButtonResponse::class, $paymentButtonResponse);
		$this->assertSame('123456789', $paymentButtonResponse->getPayId());
		$this->assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $paymentButtonResponse->getResponseDateTime());
		$this->assertEquals(ResultCode::get(ResultCode::C0_OK), $paymentButtonResponse->getResultCode());
		$this->assertSame('OK', $paymentButtonResponse->getResultMessage());
		$this->assertEquals(PaymentStatus::get(PaymentStatus::S1_CREATED), $paymentButtonResponse->getPaymentStatus());
		$this->assertSame('https://platebnibrana.csob.cz/pay/vasobchod.cz/2c72d818-9788-45a1-878a-9db2a706edc5/pt-detect/csob', $paymentButtonResponse->getRedirectUrl());
		$this->assertSame(HttpMethod::get(HttpMethod::GET), $paymentButtonResponse->getRedirectMethod());
		$this->assertNull($paymentButtonResponse->getRedirectParams());
	}

}
