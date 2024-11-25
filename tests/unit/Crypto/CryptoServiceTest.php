<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Crypto;

use PHPUnit\Framework\TestCase;

class CryptoServiceTest extends TestCase
{

	private CryptoService $cryptoService;

	protected function setUp(): void
	{
		$this->cryptoService = new CryptoService(
			__DIR__ . '/../../keys/client.key',
			__DIR__ . '/../../keys/client.pub',
		);
	}

	/**
	 * @return mixed[]
	 */
	public static function getSignDataData(): array
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
				'YlsQZVFnYZTu9oqsRTtPCBz8EDVfvPq52LZs3DxuOP7p3UGQA+Eu2Q9G/xNB4Sb0WwxSrt17yUmvjCa4vxeU5rGc19Pvv+4jznkL7DWdhETSVzChomxOwmuZ0mM3yWepMgxjZgo/j31ilv+8JMItvG4270qZ99Vfl4MWo45vI7bqqA6eUaNYk6rL76gFkRNcwTfHmP9iRMOo1N6wtmaedlbqAEvvflVfLnyyRXhT7B9iJYMGXIh4LxvbLQfC8YuJKbW+D2coVcobeFrO4lX7uLGXSMYP2o0QW6U+WH3NyFtL3Unh2qJCKnoBRVKAsiQLfxk68wfBwrFdC6O5D+show==',
				true,
				new SignatureDataFormatter([
					'id' => null,
					'name' => null,
					'cart' => [
						[
							'name' => null,
							'price' => null,
						],
					],
					'description' => null,
				]),
			],
			[
				[
					'merchantId' => '012345',
					'orderNo' => '5547',
					'dttm' => '20140425131559',
				],
				'invalidSignature',
				false,
				new SignatureDataFormatter([]),
			],
		];
	}

	/**
	 * @dataProvider getSignDataData
	 *
	 * @param mixed[] $data
	 */
	public function testSignData(array $data, string $expectedSignature, bool $valid, SignatureDataFormatter $signatureDataFormatter): void
	{
		$signature = $this->cryptoService->signData($data, $signatureDataFormatter);

		if ($valid) {
			self::assertSame($expectedSignature, $signature);
		} else {
			self::assertNotSame($expectedSignature, $signature);
		}
	}

	public function testExceptions(): void
	{
		$cryptoService = new CryptoService(
			__DIR__ . '/invalid-key.key',
			__DIR__ . '/invalid-key.key',
		);

		try {
			$cryptoService->signData([], new SignatureDataFormatter([]));
			self::fail();

		} catch (PrivateKeyFileException $e) {
			self::assertSame(__DIR__ . '/invalid-key.key', $e->getPrivateKeyFile());
		}

		try {
			$cryptoService->verifyData([], 'fooSignature', new SignatureDataFormatter([]));
			self::fail();

		} catch (PublicKeyFileException $e) {
			self::assertSame(__DIR__ . '/invalid-key.key', $e->getPublicKeyFile());
		}
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testExceptions2(): void
	{
		include __DIR__ . '/GlobalFunctionsMock.php';

		$cryptoService = new CryptoService(
			__DIR__ . '/../../keys/client.key',
			__DIR__ . '/../../keys/bank.pub',
		);

		try {
			$cryptoService->signData([], new SignatureDataFormatter([]));
			self::fail();

		} catch (SigningFailedException $e) {
			self::assertSame([], $e->getData());
		}

		try {
			$cryptoService->verifyData([], 'fooSignature', new SignatureDataFormatter([]));
			self::fail();

		} catch (VerificationFailedException $e) {
			self::assertSame([], $e->getData());
			self::assertSame('error_message', $e->getErrorMessage());
		}
	}

	/**
	 * @dataProvider getSignDataData
	 *
	 * @param mixed[] $data
	 */
	public function testVerifyData(array $data, string $signature, bool $valid, SignatureDataFormatter $signatureDataFormatter): void
	{
		if ($valid) {
			self::assertTrue($this->cryptoService->verifyData($data, $signature, $signatureDataFormatter));
		} else {
			self::assertFalse($this->cryptoService->verifyData($data, $signature, $signatureDataFormatter));
		}
	}

}
