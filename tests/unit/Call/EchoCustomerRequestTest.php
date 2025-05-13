<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class EchoCustomerRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('post')
			->with('echo/customer', [
				'merchantId' => '012345',
				'customerId' => 'cust123@mail.com',
			])
			->willReturn(
				new Response(ResponseCode::S200_OK, [
					'customerId' => 'cust123@mail.com',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
				]),
			);

		$customerInfoRequest = new EchoCustomerRequest(
			'012345',
			'cust123@mail.com',
		);

		$response = $customerInfoRequest->send($apiClient);

		self::assertSame('cust123@mail.com', $response->getCustomerId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $response->getResponseDateTime());
		self::assertSame(ResultCode::C0_OK, $response->getResultCode());
		self::assertSame('OK', $response->getResultMessage());
	}

}
