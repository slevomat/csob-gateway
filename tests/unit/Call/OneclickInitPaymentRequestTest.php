<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Currency;
use SlevomatCsobGateway\Price;

class OneclickInitPaymentRequestTest extends \PHPUnit_Framework_TestCase
{

	public function testSend()
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('payment/oneclick/init', [
				'merchantId' => '012345',
				'origPayId' => 'ef08b6e9f22345c',
				'orderNo' => '5547',
				'totalAmount' => 1789600,
				'currency' => 'CZK',
				'description' => 'Nákup na vasobchod.cz (Lenovo ThinkPad Edge E540, Doprava PPL)',
			])
			->willReturn(
				new Response(new ResponseCode(ResponseCode::S200_OK), [
					'payId' => '123456789',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 1,
				])
			);

		$initPaymentRequest = new OneclickInitPaymentRequest(
			'012345',
			'ef08b6e9f22345c',
			'5547',
			new Price(1789600, new Currency(Currency::CZK)),
			'Nákup na vasobchod.cz (Lenovo ThinkPad Edge E540, Doprava PPL)'
		);

		/** @var ApiClient $apiClient */
		$paymentResponse = $initPaymentRequest->send($apiClient);

		$this->assertInstanceOf(PaymentResponse::class, $paymentResponse);
		$this->assertSame('123456789', $paymentResponse->getPayId());
		$this->assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $paymentResponse->getResponseDateTime());
		$this->assertEquals(new ResultCode(ResultCode::C0_OK), $paymentResponse->getResultCode());
		$this->assertSame('OK', $paymentResponse->getResultMessage());
		$this->assertEquals(new PaymentStatus(PaymentStatus::S1_CREATED), $paymentResponse->getPaymentStatus());
		$this->assertNull($paymentResponse->getAuthCode());
	}

}
