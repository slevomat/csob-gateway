<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Crypto;

class SignatureDataFormatterTest extends \PHPUnit\Framework\TestCase
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
						'name' => null,
						'price' => null,
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
		];
	}

	/**
	 * @param mixed[] $keyPriority
	 * @param mixed[] $data
	 * @param string $expectedData
	 *
	 * @dataProvider getFormatDataForSignatureData
	 */
	public function testFormatDataForSignature(array $keyPriority, array $data, string $expectedData)
	{
		$signatureDataFormatter = new SignatureDataFormatter($keyPriority);

		$this->assertSame($expectedData, $signatureDataFormatter->formatDataForSignature($data));
	}

}
