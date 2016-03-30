<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

use SlevomatCsobGateway\Crypto\CryptoService;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;

class ApiClientTest extends \PHPUnit_Framework_TestCase
{

	const API_URL = 'http://foo.csob.cz';

	public function getRequests()
	{
		return [
			[
				new HttpMethod(HttpMethod::GET),
				'fooUrl/{dttm}/{signature}',
				'fooUrl/' . date('YmdHis') . '/signature',
				[],
				null,
				[
					'bar' => 2,
				],
				new ResponseCode(ResponseCode::S200_OK),
				[
					'header' => 'value',
				],
			],
			[
				new HttpMethod(HttpMethod::GET),
				'fooUrl/{fooId}/{dttm}/{signature}',
				'fooUrl/3/' . date('YmdHis') . '/signature',
				[
					'fooId' => 3,
				],
				null,
				[
					'bar' => 2,
				],
				new ResponseCode(ResponseCode::S200_OK),
				[
					'header' => 'value',
				],
			],
			[
				new HttpMethod(HttpMethod::POST),
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
				new ResponseCode(ResponseCode::S200_OK),
				[
					'header' => 'value',
				],
			],
			[
				new HttpMethod(HttpMethod::PUT),
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
				new ResponseCode(ResponseCode::S200_OK),
				[
					'header' => 'value',
				],
			],
			[
				new HttpMethod(HttpMethod::GET),
				'fooUrl/{dttm}/{signature}',
				'fooUrl/' . date('YmdHis') . '/signature',
				[],
				null,
				null,
				new ResponseCode(ResponseCode::S303_SEE_OTHER),
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
		self::markTestIncomplete('Contains imprecise DateTime handling');
		$currentTime = new \DateTimeImmutable();

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
				->with($httpMethod, self::API_URL . '/' . $expectedUrl, $expectedRequestData)
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
				->with($httpMethod, self::API_URL . '/' . $expectedUrl, $expectedRequestData + [
						'signature' => $cryptoService->signData($requestData, new SignatureDataFormatter([])),
						'dttm' => $currentTime->format('YmdHis'),
					])
				->willReturn(new Response(
					$responseCode,
					($responseData !== null ? $responseData : []) + [
						'signature' => 'signature',
					],
					$responseHeaders
				));
		}

		/** @var ApiClientDriver $apiClientDriver */
		$apiClient = new ApiClient($apiClientDriver, $cryptoService, self::API_URL);

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
					new ResponseCode(ResponseCode::S400_BAD_REQUEST),
					[]
				),
				BadRequestException::class,
			],
			[
				new Response(
					new ResponseCode(ResponseCode::S403_FORBIDDEN),
					[]
				),
				ForbiddenException::class,
			],
			[
				new Response(
					new ResponseCode(ResponseCode::S404_NOT_FOUND),
					[]
				),
				NotFoundException::class,
			],
			[
				new Response(
					new ResponseCode(ResponseCode::S405_METHOD_NOT_ALLOWED),
					[]
				),
				MethodNotAllowedException::class,
			],
			[
				new Response(
					new ResponseCode(ResponseCode::S429_TOO_MANY_REQUESTS),
					[]
				),
				TooManyRequestsException::class,
			],
			[
				new Response(
					new ResponseCode(ResponseCode::S503_SERVICE_UNAVAILABLE),
					[]
				),
				ServiceUnavailableException::class,
			],
			[
				new Response(
					new ResponseCode(ResponseCode::S500_INTERNAL_ERROR),
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
			new ResponseCode(ResponseCode::S200_OK),
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
			$this->assertSame($response, $e->getResponse());
		}
	}

	public function testInvalidSignature()
	{
		$response = new Response(
			new ResponseCode(ResponseCode::S200_OK),
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
			$this->assertSame($response, $e->getResponse());
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

}
