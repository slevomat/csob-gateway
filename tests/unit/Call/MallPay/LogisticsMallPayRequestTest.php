<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\MallPay;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Currency;
use SlevomatCsobGateway\MallPay\LogisticsEvent;
use SlevomatCsobGateway\MallPay\OrderItemType;
use SlevomatCsobGateway\MallPay\OrderReference;
use SlevomatCsobGateway\MallPay\Vat;
use SlevomatCsobGateway\Price;

class LogisticsMallPayRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('put')
			->with('mallpay/logistics', [
				'merchantId' => '012345',
				'payId' => '12345',
				'event' => LogisticsEvent::SENT,
				'date' => '20210505',
				'fulfilled' => [
					'totalPrice' => [
						'amount' => 200,
						'currency' => 'EUR',
					],
					'totalVat' => [
						[
							'amount' => 40,
							'currency' => 'EUR',
							'vatRate' => 20,
						],
					],
					'items' => [
						[
							'code' => '123',
							'name' => 'Super věc',
							'ean' => '345',
							'type' => 'PHYSICAL',
							'quantity' => 2,
						],
					],
				],
				'deliveryTrackingNumber' => '876',
			])
			->willReturn(
				new Response(ResponseCode::get(ResponseCode::S200_OK), [
					'payId' => '123456789',
					'dttm' => '20210505092159',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 1,
					'mallpayUrl' => 'https://mallpay.cz',
				])
			);

		$orderReference = new OrderReference(
			new Price(200, Currency::get(Currency::EUR)),
			[
				new Vat(40, Currency::get(Currency::EUR), 20),
			]
		);
		$orderReference->addItem('123', '345', 'Super věc', OrderItemType::get(OrderItemType::PHYSICAL), 2);

		$request = new LogisticsMallPayRequest(
			'012345',
			'12345',
			LogisticsEvent::get(LogisticsEvent::SENT),
			new DateTimeImmutable('2021-05-05 09:21:59'),
			$orderReference,
			null,
			'876'
		);

		$response = $request->send($apiClient);

		self::assertSame('123456789', $response->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20210505092159'), $response->getResponseDateTime());
		self::assertEquals(ResultCode::get(ResultCode::C0_OK), $response->getResultCode());
		self::assertSame('OK', $response->getResultMessage());
		self::assertEquals(PaymentStatus::get(PaymentStatus::S1_CREATED), $response->getPaymentStatus());
	}

}
