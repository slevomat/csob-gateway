<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Crypto;

use PHPUnit\Framework\TestCase;

class SignatureDataFormatterTest extends TestCase
{

	/**
	 * @return mixed[]
	 */
	public function getFormatDataForSignatureData(): array
	{
		return [
			[
				[
					'id' => null,
					'name' => null,
				],
				[
					'id' => 123,
					'name' => 'foo',
				],
				'123|foo',
			],
			[
				[
					'id' => null,
					'name' => null,
				],
				[
					'name' => 'foo',
					'id' => 123,
				],
				'123|foo',
			],
			[
				[
					'id' => null,
					'name' => null,
				],
				[
					'name' => 'foo',
					'id' => 123,
					'date' => '2015-10-10',
				],
				'123|foo',
			],
			[
				[
					'id' => null,
					'name' => null,
					'date' => null,
				],
				[
					'name' => 'foo',
					'id' => 123,
				],
				'123|foo',
			],
			[
				[
					'id' => null,
					'name' => null,
					'cart' => [
						[
							'name' => null,
							'price' => null,
						],
					],
					'description' => null,
				],
				[
					'name' => 'foo',
					'id' => 123,
					'cart' => [
						[
							'price' => 99,
							'name' => 'foo product',
						],
						[
							'name' => 'bar product',
						],
					],
					'description' => 'order description',
				],
				'123|foo|foo product|99|bar product|order description',
			],
			[
				[
					'payId' => null,
					'dttm' => null,
					'resultCode' => null,
					'resultMessage' => null,
					'paymentStatus' => null,
					'lightboxParams' => [
						'requestToken' => null,
						'callbackUrl' => null,
						'merchantCheckoutId' => null,
						'allowedCardTypes' => null,
						'suppressShippingAddressEnable' => null,
						'loyaltyEnabled' => null,
						'version' => null,
						'shippingLocationProfile' => null,
						'agreeToS' => null,
					],
				],
				[
					'dttm' => '2017',
					'payId' => '123',
					'resultCode' => 0,
					'resultMessage' => 'OK',
					'paymentStatus' => 1,
					'lightboxParams' => [
						'requestToken' => '261666',
						'callbackUrl' => 'https:/example.com/',
						'merchantCheckoutId' => 'DUMMY',
						'allowedCardTypes' => 'visa,master',
						'suppressShippingAddressEnable' => 'true',
						'loyaltyEnabled' => 'false',
						'version' => 'v6',
						'shippingLocationProfile' => 'SP-0001',
						'agreeToS' => true,
					],
				],
				'123|2017|0|OK|1|261666|https:/example.com/|DUMMY|visa,master|true|false|v6|SP-0001|true',
			],
			[
				[
					'dttm' => null,
					'resultCode' => null,
					'resultMessage' => null,
					'checkoutParams' => [
						'apiVersion' => null,
						'apiVersionMinor' => null,
						'paymentMethodType' => null,
						'allowedCardNetworks' => [],
						'allowedCardAuthMethods' => [],
					],
				],
				[
					'dttm' => '2017',
					'resultCode' => 0,
					'checkoutParams' => [
						'apiVersion' => 2,
						'apiVersionMinor' => 1,
						'paymentMethodType' => 'CARD',
						'allowedCardNetworks' => ['VISA', 'MASTERCARD'],
						'allowedCardAuthMethods' => ['PAN_ONLY'],
					],
				],
				'2017|0|2|1|CARD|VISA|MASTERCARD|PAN_ONLY',
			],
		];
	}

	/**
	 * @dataProvider getFormatDataForSignatureData
	 *
	 * @param mixed[] $keyPriority
	 * @param mixed[] $data
	 * @param string $expectedData
	 */
	public function testFormatDataForSignature(array $keyPriority, array $data, string $expectedData): void
	{
		$signatureDataFormatter = new SignatureDataFormatter($keyPriority);

		self::assertSame($expectedData, $signatureDataFormatter->formatDataForSignature($data));
	}

}
