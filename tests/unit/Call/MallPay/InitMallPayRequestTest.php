<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\MallPay;

use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\HttpMethod;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Currency;
use SlevomatCsobGateway\MallPay\AddressType;
use SlevomatCsobGateway\MallPay\Country;
use SlevomatCsobGateway\MallPay\Customer;
use SlevomatCsobGateway\MallPay\Order;
use SlevomatCsobGateway\MallPay\OrderCarrierId;
use SlevomatCsobGateway\MallPay\OrderDeliveryType;
use SlevomatCsobGateway\MallPay\OrderItemType;

class InitMallPayRequestTest extends TestCase
{

	public function testSend(): void
	{
		/** @var ApiClient|MockObject $apiClient */
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('mallpay/init', [
				'merchantId' => '012345',
				'orderNo' => '12345',
				'customer' => [
					'email' => 'pepa@zdepa.cz',
					'phone' => '+420800300300',
					'fullName' => 'Pepa Zdepa',
					'titleBefore' => 'Ing',
					'titleAfter' => 'Ph.d',
					'tin' => '123',
					'vatin' => '345',
				],
				'order' => [
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
					'addresses' => [
						[
							'country' => 'CZ',
							'city' => 'Praha 8',
							'streetAddress' => 'Pernerova 691/42',
							'zip' => '186 00',
							'addressType' => 'BILLING',
							'name' => 'Slevomat',
							'streetNumber' => 'xxx',
						],
					],
					'items' => [
						[
							'code' => '123',
							'name' => 'Super věc',
							'totalPrice' => [
								'amount' => 200,
								'currency' => 'EUR',
							],
							'totalVat' => [
								'amount' => 40,
								'currency' => 'EUR',
								'vatRate' => 20,
							],
							'ean' => '345',
							'type' => 'PHYSICAL',
							'quantity' => 2,
							'variant' => 'Varianta 1',
							'description' => 'Popisek',
							'producer' => 'Producer',
							'categories' => ['kategorie 1', 'kategorie 2'],
							'unitPrice' => [
								'amount' => 100,
								'currency' => 'EUR',
							],
							'unitVat' => [
								'amount' => 20,
								'currency' => 'EUR',
								'vatRate' => 20,
							],
							'productUrl' => 'https://obchod.cz/produkt/123-345',
						],
					],
					'deliveryType' => 'DELIVERY_CARRIER',
					'carrierId' => 'TNT',
				],
				'agreeTC' => true,
				'clientIp' => '127.0.0.1',
				'returnUrl' => 'https://www.slevomat.cz',
				'returnMethod' => 'GET',
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

		$order = new Order(Currency::get(Currency::EUR), OrderDeliveryType::get(OrderDeliveryType::DELIVERY_CARRIER), OrderCarrierId::get(OrderCarrierId::TNT), null);
		$order->addItem(
			'123',
			'345',
			'Super věc',
			OrderItemType::get(OrderItemType::PHYSICAL),
			2,
			'Varianta 1',
			'Popisek',
			'Producer',
			['kategorie 1', 'kategorie 2'],
			100,
			200,
			20,
			40,
			20,
			'https://obchod.cz/produkt/123-345'
		);
		$order->addAddress('Slevomat', Country::get(Country::CZE), 'Praha 8', 'Pernerova 691/42', 'xxx', '186 00', AddressType::get(AddressType::BILLING));

		$request = new InitMallPayRequest(
			'012345',
			'12345',
			new Customer(null, null, 'Pepa Zdepa', 'Ing', 'Ph.d', 'pepa@zdepa.cz', '+420800300300', '123', '345'),
			$order,
			true,
			'127.0.0.1',
			HttpMethod::get(HttpMethod::GET),
			'https://www.slevomat.cz',
			null,
			null
		);

		$response = $request->send($apiClient);

		self::assertSame('123456789', $response->getPayId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20210505092159'), $response->getResponseDateTime());
		self::assertEquals(ResultCode::get(ResultCode::C0_OK), $response->getResultCode());
		self::assertSame('OK', $response->getResultMessage());
		self::assertEquals(PaymentStatus::get(PaymentStatus::S1_CREATED), $response->getPaymentStatus());
		self::assertSame('https://mallpay.cz', $response->getMallpayUrl());
	}

}
