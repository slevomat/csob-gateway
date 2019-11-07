<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class CustomerInfoRequestTest extends TestCase
{

	public function testSend(): void
	{
		$apiClient = $this->getMockBuilder(ApiClient::class)
			->disableOriginalConstructor()
			->getMock();

		$apiClient->expects(self::once())->method('get')
			->with('customer/info/{merchantId}/{customerId}/{dttm}/{signature}', [
				'merchantId' => '012345',
				'customerId' => 'cust123@mail.com',
			])
			->willReturn(
				new Response(ResponseCode::get(ResponseCode::S200_OK), [
					'customerId' => 'cust123@mail.com',
					'dttm' => '20140425131559',
					'resultCode' => 0,
					'resultMessage' => 'OK',
				])
			);

		/** @var ApiClient $apiClient */
		$customerInfoRequest = new CustomerInfoRequest(
			'012345',
			'cust123@mail.com'
		);

		$customerInfoResponse = $customerInfoRequest->send($apiClient);

		self::assertSame('cust123@mail.com', $customerInfoResponse->getCustomerId());
		self::assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $customerInfoResponse->getResponseDateTime());
		self::assertEquals(ResultCode::get(ResultCode::C0_OK), $customerInfoResponse->getResultCode());
		self::assertSame('OK', $customerInfoResponse->getResultMessage());
	}

}
