<?php

namespace SlevomatCsobGateway\Crypto;

class CryptoServiceTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var CryptoService
	 */
	private $cryptoService;

	protected function setUp()
	{
		$this->cryptoService = new CryptoService(
			__DIR__ . '/../../keys/client.key',
			__DIR__ . '/../../keys/client.pub',
			__DIR__ . '/../../keys/client.pub'
		);
	}

	public function getSignDataData()
	{
		return [
			[
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
				'VW1Ku7Ekvr2mxpF4bcAt8pViD60oMn7Ktifv7VO9CQ5aPoacICyRnx6csrtAhmhZ+3W5aA1PmSnlZEQ8+GkTpKk9pvS7vOQgyR1+62bsz5nlFigQ5esfoCAgcq4noWl1xQ2f9ScXn5gh/8iSDWTZ4BJvyddtgBP2eY4qzk8Lk7lrpksR8jbs1dLDLbYD8/LynZo4eD7OeKewwdG++bdSvYKKEHVg7CWT6JVFgucM1v59N1C+Qz+IihZ0gUuQbJxJWNe3vI/X6sTLdqqJTMw/MDghfMkBWC2L1yZbqOUd6LQwtg82KFZFZB71e71su60ci4TujvGErzLt1I+SJ7SWQg==',
				true,
				new SignatureDataFormatter([
					'id' => null,
					'name' => null,
					'cart' => [
						'name' => null,
						'price' => null,
					],
					'description' => null,
				])
			],
			[
				[
					'merchantId' => '012345',
					'orderNo' => '5547',
					'dttm' => '20140425131559',
				],
				'invalidSignature',
				false,
				new SignatureDataFormatter([])
			]
		];
	}

	/**
	 * @param mixed[] $data
	 * @param string $expectedSignature
	 * @param bool $valid
	 * @param SignatureDataFormatter $signatureDataFormatter
	 *
	 * @dataProvider getSignDataData
	 */
	public function testSignData(array $data, $expectedSignature, $valid, SignatureDataFormatter $signatureDataFormatter)
	{
		$signature = $this->cryptoService->signData($data, $signatureDataFormatter);

		if ($valid) {
			$this->assertSame($expectedSignature, $signature);
		} else {
			$this->assertNotSame($expectedSignature, $signature);
		}
	}

	public function testExceptions()
	{
		$cryptoService = new CryptoService(
			__DIR__ . '/invalid-key.key',
			__DIR__ . '/invalid-key.key',
			__DIR__ . '/invalid-key.key'
		);

		try {
			$cryptoService->signData([], new SignatureDataFormatter([]));
			$this->fail();

		} catch (PrivateKeyFileException $e) {
			$this->assertSame(__DIR__ . '/invalid-key.key', $e->getPrivateKeyFile());
		}

		try {
			$cryptoService->verifyData([], 'fooSignature', new SignatureDataFormatter([]));
			$this->fail();

		} catch (PublicKeyFileException $e) {
			$this->assertSame(__DIR__ . '/invalid-key.key', $e->getPublicKeyFile());
		}
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testExceptions2()
	{
		include __DIR__ . '/GlobalFunctionsMock.php';

		$cryptoService = new CryptoService(
			__DIR__ . '/../../keys/client.key',
			__DIR__ . '/../../keys/client.pub',
			__DIR__ . '/../../keys/bank.pub'
		);

		try {
			$cryptoService->signData([], new SignatureDataFormatter([]));
			$this->fail();

		} catch (SigningFailedException $e) {
			$this->assertSame([], $e->getData());
		}

		try {
			$cryptoService->verifyData([], 'fooSignature', new SignatureDataFormatter([]));
			$this->fail();

		} catch (VerificationFailedException $e) {
			$this->assertSame([], $e->getData());
			$this->assertSame('error_message', $e->getErrorMessage());
		}
	}

	/**
	 * @param mixed[] $data
	 * @param string $signature
	 * @param bool $valid
	 * @param SignatureDataFormatter $signatureDataFormatter
	 *
	 * @dataProvider getSignDataData
	 */
	public function testVerifyData(array $data, $signature, $valid, SignatureDataFormatter $signatureDataFormatter)
	{
		if ($valid) {
			$this->assertTrue($this->cryptoService->verifyData($data, $signature, $signatureDataFormatter));
		} else {
			$this->assertFalse($this->cryptoService->verifyData($data, $signature, $signatureDataFormatter));
		}
	}

}
