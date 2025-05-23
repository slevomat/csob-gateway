<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

use PHPUnit\Framework\Constraint\RegularExpression;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use SlevomatCsobGateway\Call\ResponseExtensionHandler;
use SlevomatCsobGateway\Crypto\CryptoService;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use stdClass;
use function preg_quote;
use function sprintf;

class ApiClientTest extends TestCase
{

	private const API_URL = 'http://foo.csob.cz';

	/**
	 * @return mixed[]
	 */
	public static function getRequests(): array
	{
		return [
			[
				HttpMethod::GET,
				'fooUrl/{dttm}/{signature}',
				'fooUrl/\\d{14}/signature',
				[],
				null,
				[
					'bar' => 2,
				],
				ResponseCode::S200_OK,
				[
					'header' => 'value',
				],
			],
			[
				HttpMethod::GET,
				'fooUrl/{fooId}/{dttm}/{signature}',
				'fooUrl/3/\\d{14}/signature',
				[
					'fooId' => 3,
				],
				null,
				[
					'bar' => 2,
				],
				ResponseCode::S200_OK,
				[
					'header' => 'value',
				],
			],
			[
				HttpMethod::POST,
				'fooUrl',
				'fooUrl',
				[
					'foo' => 1,
				],
				[
					'foo' => 1,
				],
				[
					'bar' => 2,
				],
				ResponseCode::S200_OK,
				[
					'header' => 'value',
				],
			],
			[
				HttpMethod::PUT,
				'fooUrl',
				'fooUrl',
				[
					'foo' => 1,
				],
				[
					'foo' => 1,
				],
				[
					'bar' => 2,
				],
				ResponseCode::S200_OK,
				[
					'header' => 'value',
				],
			],
			[
				HttpMethod::GET,
				'fooUrl/{dttm}/{signature}',
				'fooUrl/\\d{14}/signature',
				[],
				null,
				null,
				ResponseCode::S303_SEE_OTHER,
				[
					'header' => 'value',
				],
			],
		];
	}

	/**
	 * @dataProvider getRequests
	 *
	 * @param mixed[] $requestData
	 * @param mixed[]|null $expectedRequestData
	 * @param mixed[]|null $responseData
	 * @param mixed[] $responseHeaders
	 */
	public function testRequests(
		HttpMethod $httpMethod,
		string $url,
		string $expectedUrl,
		array $requestData,
		?array $expectedRequestData,
		?array $responseData,
		ResponseCode $responseCode,
		array $responseHeaders,
	): void
	{
		$cryptoService = $this->getMockBuilder(CryptoService::class)
			->disableOriginalConstructor()
			->getMock();

		$cryptoService
			->method('signData')
			->willReturn('signature');

		$cryptoService
			->method('verifyData')
			->willReturn(true);

		$apiClientDriver = $this->getMockBuilder(ApiClientDriver::class)
			->getMock();

		if ($httpMethod === HttpMethod::GET) {
			$apiClientDriver->expects(self::once())
				->method('request')
				->with($httpMethod, self::matchesRegularExpression(sprintf('~^%s/%s$~', preg_quote(self::API_URL, '~'), $expectedUrl)), $expectedRequestData)
				->willReturn(new Response(
					$responseCode,
					($responseData ?? []) + [
						'signature' => 'signature',
					],
					$responseHeaders,
				));

		} else {
			$apiClientDriver->expects(self::once())
				->method('request')
				->willReturnCallback(static function (HttpMethod $method, string $url, array $requestData) use ($httpMethod, $expectedUrl, $expectedRequestData, $responseCode, $responseData, $responseHeaders): Response {
					self::assertEquals($httpMethod, $method);
					self::assertSame(sprintf('%s/%s', self::API_URL, $expectedUrl), $url);
					$dttm = $requestData['dttm'];
					static::assertThat($dttm, new RegularExpression('~^\\d{14}$~'), '');
					unset($requestData['dttm']);
					self::assertEquals(((array) $expectedRequestData) + ['signature' => 'signature'], $requestData);

					return new Response(
						$responseCode,
						($responseData ?? []) + [
							'signature' => 'signature',
						],
						$responseHeaders,
					);
				});
		}

		$logger = $this->getMockBuilder(LoggerInterface::class)
			->disableOriginalConstructor()
			->getMock();

		$logger->expects(self::once())
			->method('info')
			->with(self::isType('string'), self::isType('array'));

		$apiClient = new ApiClient($apiClientDriver, $cryptoService, self::API_URL);
		$apiClient->setLogger($logger);

		if ($httpMethod === HttpMethod::GET) {
			$response = $apiClient->get($url, $requestData, new SignatureDataFormatter([]), new SignatureDataFormatter([]));

		} elseif ($httpMethod === HttpMethod::POST) {
			$response = $apiClient->post($url, $requestData, new SignatureDataFormatter([]), new SignatureDataFormatter([]));

		} else {
			$response = $apiClient->put($url, $requestData, new SignatureDataFormatter([]), new SignatureDataFormatter([]));
		}

		self::assertSame($responseCode->value, $response->getResponseCode()->value);
		self::assertEquals($responseHeaders, $response->getHeaders());
		self::assertEquals($responseData, $response->getData());
	}

	/**
	 * @return mixed[]
	 */
	public static function getTestExceptions(): array
	{
		return [
			[
				new Response(
					ResponseCode::S400_BAD_REQUEST,
					[],
				),
				BadRequestException::class,
			],
			[
				new Response(
					ResponseCode::S403_FORBIDDEN,
					[],
				),
				ForbiddenException::class,
			],
			[
				new Response(
					ResponseCode::S404_NOT_FOUND,
					[],
				),
				NotFoundException::class,
			],
			[
				new Response(
					ResponseCode::S405_METHOD_NOT_ALLOWED,
					[],
				),
				MethodNotAllowedException::class,
			],
			[
				new Response(
					ResponseCode::S429_TOO_MANY_REQUESTS,
					[],
				),
				TooManyRequestsException::class,
			],
			[
				new Response(
					ResponseCode::S503_SERVICE_UNAVAILABLE,
					[],
				),
				ServiceUnavailableException::class,
			],
			[
				new Response(
					ResponseCode::S500_INTERNAL_ERROR,
					[],
				),
				InternalErrorException::class,
			],
		];
	}

	/**
	 * @dataProvider getTestExceptions
	 *
	 * @phpstan-param class-string $expectedExceptionClass
	 */
	public function testExceptions(Response $response, string $expectedExceptionClass): void
	{
		$cryptoService = $this->getMockBuilder(CryptoService::class)
			->disableOriginalConstructor()
			->getMock();

		$cryptoService->expects(self::once())
			->method('signData')
			->willReturn('signature');

		$cryptoService
			->method('verifyData')
			->willReturn(true);

		$apiClientDriver = $this->getMockBuilder(ApiClientDriver::class)
			->getMock();

		$apiClientDriver->expects(self::once())
			->method('request')
			->willReturn($response);

		$apiClient = new ApiClient($apiClientDriver, $cryptoService, self::API_URL);

		try {
			$apiClient->get('foo/{dttm}/{signature}', [], new SignatureDataFormatter([]), new SignatureDataFormatter([]));
			self::fail();

		} catch (RequestException $e) {
			self::assertInstanceOf($expectedExceptionClass, $e);
			self::assertSame($response, $e->getResponse());
		}
	}

	public function testMissingSignature(): void
	{
		$response = new Response(
			ResponseCode::S200_OK,
			[],
		);

		$cryptoService = $this->getMockBuilder(CryptoService::class)
			->disableOriginalConstructor()
			->getMock();

		$cryptoService->expects(self::once())
			->method('signData')
			->willReturn('signature');

		$apiClientDriver = $this->getMockBuilder(ApiClientDriver::class)
			->getMock();

		$apiClientDriver->expects(self::once())
			->method('request')
			->willReturn($response);
		$apiClient = new ApiClient($apiClientDriver, $cryptoService, self::API_URL);

		try {
			$apiClient->get('foo/{dttm}/{signature}', [], new SignatureDataFormatter([]), new SignatureDataFormatter([]));
			self::fail();

		} catch (InvalidSignatureException $e) {
			self::assertSame($response->getData(), $e->getResponseData());
		}
	}

	public function testInvalidSignature(): void
	{
		$response = new Response(
			ResponseCode::S200_OK,
			[
				'signature' => 'invalidSignature',
			],
		);

		$cryptoService = $this->getMockBuilder(CryptoService::class)
			->disableOriginalConstructor()
			->getMock();

		$cryptoService->expects(self::once())
			->method('signData')
			->willReturn('signature');

		$cryptoService
			->method('verifyData')
			->willReturn(false);

		$apiClientDriver = $this->getMockBuilder(ApiClientDriver::class)
			->getMock();

		$apiClientDriver->expects(self::once())
			->method('request')
			->willReturn($response);

		$apiClient = new ApiClient($apiClientDriver, $cryptoService, self::API_URL);

		try {
			$apiClient->get('foo/{dttm}/{signature}', [], new SignatureDataFormatter([]), new SignatureDataFormatter([]));
			self::fail();

		} catch (InvalidSignatureException $e) {
			$responseData = $response->getData();
			unset($responseData['signature']);
			self::assertSame($responseData, $e->getResponseData());
		}
	}

	public function testCreateResponseByData(): void
	{
		$data = [
			'signature' => 'abc',
			'foo' => 123,
			'bar' => 456,
		];

		$cryptoService = $this->getMockBuilder(CryptoService::class)
			->disableOriginalConstructor()
			->getMock();

		$cryptoService
			->method('verifyData')
			->willReturn(true);

		$apiClientDriver = $this->getMockBuilder(ApiClientDriver::class)
			->getMock();

		$apiClient = new ApiClient($apiClientDriver, $cryptoService, self::API_URL);

		$response = $apiClient->createResponseByData($data, new SignatureDataFormatter([]));

		unset($data['signature']);

		self::assertSame(ResponseCode::S200_OK, $response->getResponseCode());
		self::assertEquals([], $response->getHeaders());
		self::assertEquals($data, $response->getData());
	}

	public function testRequestWithExtension(): void
	{
		$cryptoService = $this->getMockBuilder(CryptoService::class)
			->disableOriginalConstructor()
			->getMock();

		$cryptoService->expects(self::once())
			->method('signData')
			->willReturn('signature');

		$cryptoService->expects(self::exactly(2))
			->method('verifyData')
			->willReturn(true);

		$apiClientDriver = $this->getMockBuilder(ApiClientDriver::class)
			->getMock();

		$apiClientDriver->expects(self::once())
			->method('request')
			->willReturn(new Response(
				ResponseCode::S200_OK,
				['id' => '123', 'signature' => 'signature', 'extensions' => [['extension' => 'foo', 'foo' => 'bar', 'signature' => 'signatureExtension']]],
				[],
			));

		$apiClient = new ApiClient($apiClientDriver, $cryptoService, self::API_URL);

		$extensions = [
			'foo' => new class implements ResponseExtensionHandler {

				/**
				 * @param mixed[] $decodeData
				 */
				public function createResponse(array $decodeData): stdClass
				{
					return (object) ['foo' => 'bar'];
				}

				public function getSignatureDataFormatter(): SignatureDataFormatter
				{
					return new SignatureDataFormatter([]);
				}

			},
		];

		$response = $apiClient->get('payment/status/{dttm}/{signature}', [], new SignatureDataFormatter([]), new SignatureDataFormatter([]), null, $extensions);

		self::assertSame(ResponseCode::S200_OK, $response->getResponseCode());
		self::assertEquals([], $response->getHeaders());
		self::assertEquals(['id' => '123'], $response->getData());
		self::assertCount(1, $response->getExtensions());
		self::assertInstanceOf(stdClass::class, $response->getExtensions()['foo']);
		self::assertSame('bar', $response->getExtensions()['foo']->foo);
	}

}
