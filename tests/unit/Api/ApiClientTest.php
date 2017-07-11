<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

use Psr\Log\LoggerInterface;
use SlevomatCsobGateway\Crypto\CryptoService;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;

class ApiClientTest extends \PHPUnit\Framework\TestCase
{

	const API_URL = 'http://foo.csob.cz';

	/**
	 * @return mixed[]
	 */
	public function getRequests(): array
	{
		return [
			[
				HttpMethod::get(HttpMethod::GET),
				'fooUrl/{dttm}/{signature}',
				'fooUrl/\\d{14}/signature',
				[],
				null,
				[
					'bar' => 2,
				],
				ResponseCode::get(ResponseCode::S200_OK),
				[
					'header' => 'value',
				],
			],
			[
				HttpMethod::get(HttpMethod::GET),
				'fooUrl/{fooId}/{dttm}/{signature}',
				'fooUrl/3/\\d{14}/signature',
				[
					'fooId' => 3,
				],
				null,
				[
					'bar' => 2,
				],
				ResponseCode::get(ResponseCode::S200_OK),
				[
					'header' => 'value',
				],
			],
			[
				HttpMethod::get(HttpMethod::POST),
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
				ResponseCode::get(ResponseCode::S200_OK),
				[
					'header' => 'value',
				],
			],
			[
				HttpMethod::get(HttpMethod::PUT),
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
				ResponseCode::get(ResponseCode::S200_OK),
				[
					'header' => 'value',
				],
			],
			[
				HttpMethod::get(HttpMethod::GET),
				'fooUrl/{dttm}/{signature}',
				'fooUrl/\\d{14}/signature',
				[],
				null,
				null,
				ResponseCode::get(ResponseCode::S303_SEE_OTHER),
				[
					'header' => 'value',
				],
			],
		];
	}

	/**
	 * @param HttpMethod $httpMethod
	 * @param string $url
	 * @param string $expectedUrl
	 * @param mixed[] $requestData
	 * @param mixed[]|null $expectedRequestData
	 * @param mixed[]|null $responseData
	 * @param ResponseCode $responseCode
	 * @param mixed[] $responseHeaders
	 *
	 * @dataProvider getRequests
	 */
	public function testRequests(HttpMethod $httpMethod, string $url, string $expectedUrl, array $requestData, array $expectedRequestData = null, array $responseData = null, ResponseCode $responseCode, array $responseHeaders)
	{
		$cryptoService = $this->getMockBuilder(CryptoService::class)
			->disableOriginalConstructor()
			->getMock();

		$cryptoService->expects(self::any())
			->method('signData')
			->willReturn('signature');

		$cryptoService->expects(self::any())
			->method('verifyData')
			->willReturn(true);

		/** @var CryptoService $cryptoService */
		$apiClientDriver = $this->getMockBuilder(ApiClientDriver::class)
			->getMock();

		if ($httpMethod->equalsValue(HttpMethod::GET)) {
			$apiClientDriver->expects(self::once())
				->method('request')
				->with($httpMethod, $this->matchesRegularExpression(sprintf('~^%s/%s$~', preg_quote(self::API_URL, '~'), $expectedUrl)), $expectedRequestData)
				->willReturn(new Response(
					$responseCode,
					($responseData !== null ? $responseData : []) + [
						'signature' => 'signature',
					],
					$responseHeaders
				));

		} else {
			$apiClientDriver->expects(self::once())
				->method('request')
				->willReturnCallback(function (HttpMethod $method, string $url, array $requestData) use ($httpMethod, $expectedUrl, $expectedRequestData, $responseCode, $responseData, $responseHeaders): Response {
					$this->assertEquals($httpMethod, $method);
					$this->assertSame(sprintf('%s/%s', self::API_URL, $expectedUrl), $url);
					$dttm = $requestData['dttm'];
					$this->assertRegExp('~^\\d{14}$~', $dttm);
					unset($requestData['dttm']);
					$this->assertEquals($expectedRequestData + ['signature' => 'signature'], $requestData);

					return new Response(
						$responseCode,
						($responseData !== null ? $responseData : []) + [
							'signature' => 'signature',
						],
						$responseHeaders
					);
				});
		}

		$logger = $this->getMockBuilder(LoggerInterface::class)
			->disableOriginalConstructor()
			->getMock();

		$logger->expects(self::once())
			->method('info')
			->with($this->isType('string'), $this->isType('array'));

		/** @var ApiClientDriver $apiClientDriver */
		$apiClient = new ApiClient($apiClientDriver, $cryptoService, self::API_URL);
		/** @var LoggerInterface $logger */
		$apiClient->setLogger($logger);

		if ($httpMethod->equalsValue(HttpMethod::GET)) {
			$response = $apiClient->get($url, $requestData, new SignatureDataFormatter([]), new SignatureDataFormatter([]));

		} elseif ($httpMethod->equalsValue(HttpMethod::POST)) {
			$response = $apiClient->post($url, $requestData, new SignatureDataFormatter([]), new SignatureDataFormatter([]));

		} else {
			$response = $apiClient->put($url, $requestData, new SignatureDataFormatter([]), new SignatureDataFormatter([]));
		}

		$this->assertInstanceOf(Response::class, $response);
		$this->assertSame($responseCode->getValue(), $response->getResponseCode()->getValue());
		$this->assertEquals($responseHeaders, $response->getHeaders());
		$this->assertEquals($responseData, $response->getData());
	}

	public function getTestExceptions(): array
	{
		return [
			[
				new Response(
					ResponseCode::get(ResponseCode::S400_BAD_REQUEST),
					[]
				),
				BadRequestException::class,
			],
			[
				new Response(
					ResponseCode::get(ResponseCode::S403_FORBIDDEN),
					[]
				),
				ForbiddenException::class,
			],
			[
				new Response(
					ResponseCode::get(ResponseCode::S404_NOT_FOUND),
					[]
				),
				NotFoundException::class,
			],
			[
				new Response(
					ResponseCode::get(ResponseCode::S405_METHOD_NOT_ALLOWED),
					[]
				),
				MethodNotAllowedException::class,
			],
			[
				new Response(
					ResponseCode::get(ResponseCode::S429_TOO_MANY_REQUESTS),
					[]
				),
				TooManyRequestsException::class,
			],
			[
				new Response(
					ResponseCode::get(ResponseCode::S503_SERVICE_UNAVAILABLE),
					[]
				),
				ServiceUnavailableException::class,
			],
			[
				new Response(
					ResponseCode::get(ResponseCode::S500_INTERNAL_ERROR),
					[]
				),
				InternalErrorException::class,
			],
		];
	}

	/**
	 * @param Response $response
	 * @param string $expectedExceptionClass
	 *
	 * @dataProvider getTestExceptions
	 */
	public function testExceptions(Response $response, string $expectedExceptionClass)
	{
		$cryptoService = $this->getMockBuilder(CryptoService::class)
			->disableOriginalConstructor()
			->getMock();

		$cryptoService->expects(self::once())
			->method('signData')
			->willReturn('signature');

		$cryptoService->expects(self::any())
			->method('verifyData')
			->willReturn(true);

		$apiClientDriver = $this->getMockBuilder(ApiClientDriver::class)
			->getMock();

		$apiClientDriver->expects(self::once())
			->method('request')
			->willReturn($response);

		/** @var CryptoService $cryptoService */
		/** @var ApiClientDriver $apiClientDriver */
		$apiClient = new ApiClient($apiClientDriver, $cryptoService);

		try {
			$apiClient->get('foo/{dttm}/{signature}', [], new SignatureDataFormatter([]), new SignatureDataFormatter([]));
			$this->fail();

		} catch (RequestException $e) {
			$this->assertInstanceOf($expectedExceptionClass, $e);
			$this->assertSame($response, $e->getResponse());
		}
	}

	public function testMissingSignature()
	{
		$response = new Response(
			ResponseCode::get(ResponseCode::S200_OK),
			[]
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

		/** @var CryptoService $cryptoService */
		/** @var ApiClientDriver $apiClientDriver */
		$apiClient = new ApiClient($apiClientDriver, $cryptoService);

		try {
			$apiClient->get('foo/{dttm}/{signature}', [], new SignatureDataFormatter([]), new SignatureDataFormatter([]));
			$this->fail();

		} catch (InvalidSignatureException $e) {
			$this->assertSame($response->getData(), $e->getResponseData());
		}
	}

	public function testInvalidSignature()
	{
		$response = new Response(
			ResponseCode::get(ResponseCode::S200_OK),
			[
				'signature' => 'invalidSignature',
			]
		);

		$cryptoService = $this->getMockBuilder(CryptoService::class)
			->disableOriginalConstructor()
			->getMock();

		$cryptoService->expects(self::once())
			->method('signData')
			->willReturn('signature');

		$cryptoService->expects(self::any())
			->method('verifyData')
			->willReturn(false);

		$apiClientDriver = $this->getMockBuilder(ApiClientDriver::class)
			->getMock();

		$apiClientDriver->expects(self::once())
			->method('request')
			->willReturn($response);

		/** @var CryptoService $cryptoService */
		/** @var ApiClientDriver $apiClientDriver */
		$apiClient = new ApiClient($apiClientDriver, $cryptoService);

		try {
			$apiClient->get('foo/{dttm}/{signature}', [], new SignatureDataFormatter([]), new SignatureDataFormatter([]));
			$this->fail();

		} catch (InvalidSignatureException $e) {
			$responseData = $response->getData();
			unset($responseData['signature']);
			$this->assertSame($responseData, $e->getResponseData());
		}
	}

	public function testCreateResponseByData()
	{
		$data = [
			'signature' => 'abc',
			'foo' => 123,
			'bar' => 456,
		];

		$cryptoService = $this->getMockBuilder(CryptoService::class)
			->disableOriginalConstructor()
			->getMock();

		$cryptoService->expects(self::any())
			->method('verifyData')
			->willReturn(true);

		$apiClientDriver = $this->getMockBuilder(ApiClientDriver::class)
			->getMock();

		/** @var CryptoService $cryptoService */
		/** @var ApiClientDriver $apiClientDriver */
		$apiClient = new ApiClient($apiClientDriver, $cryptoService);

		$response = $apiClient->createResponseByData($data, new SignatureDataFormatter([]));

		unset($data['signature']);

		$this->assertInstanceOf(Response::class, $response);
		$this->assertSame(ResponseCode::S200_OK, $response->getResponseCode()->getValue());
		$this->assertEquals([], $response->getHeaders());
		$this->assertEquals($data, $response->getData());
	}

	public function testRequestWithExtension()
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

		/** @var CryptoService $cryptoService */
		$apiClientDriver = $this->getMockBuilder(ApiClientDriver::class)
			->getMock();

		$apiClientDriver->expects(self::once())
			->method('request')
			->willReturn(new Response(
				ResponseCode::get(ResponseCode::S200_OK),
				['id' => '123', 'signature' => 'signature', 'extensions' => [['extension' => 'foo', 'foo' => 'bar', 'signature' => 'signatureExtension']]],
				[]
			));

		/** @var ApiClientDriver $apiClientDriver */
		$apiClient = new ApiClient($apiClientDriver, $cryptoService, self::API_URL);

		// @codingStandardsIgnoreStart
		$extensions = [
			'foo' => new class implements \SlevomatCsobGateway\Call\ResponseExtensionHandler {

				public function createResponse(array $decodeData): \stdClass
				{
					return (object) ['foo' => 'bar'];
				}

				public function getSignatureDataFormatter(): SignatureDataFormatter
				{
					return new SignatureDataFormatter([]);
				}
			},
		];
		// @codingStandardsIgnoreEnd

		$response = $apiClient->get('payment/status/{dttm}/{signature}', [], new SignatureDataFormatter([]), new SignatureDataFormatter([]), null, $extensions);

		$this->assertInstanceOf(Response::class, $response);
		$this->assertSame(ResponseCode::S200_OK, $response->getResponseCode()->getValue());
		$this->assertEquals([], $response->getHeaders());
		$this->assertEquals(['id' => '123'], $response->getData());
		$this->assertCount(1, $response->getExtensions());
		$this->assertInstanceOf(\stdClass::class, $response->getExtensions()['foo']);
		$this->assertSame('bar', $response->getExtensions()['foo']->foo);
	}

}
