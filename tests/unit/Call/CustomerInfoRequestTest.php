<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Api\Response;
use SlevomatCsobGateway\Api\ResponseCode;

class CustomerInfoRequestTest extends \PHPUnit_Framework_TestCase
{

	public function testSend()
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

		$this->assertInstanceOf(CustomerInfoResponse::class, $customerInfoResponse);
		$this->assertSame('cust123@mail.com', $customerInfoResponse->getCustomerId());
		$this->assertEquals(DateTimeImmutable::createFromFormat('YmdHis', '20140425131559'), $customerInfoResponse->getResponseDateTime());
		$this->assertEquals(ResultCode::get(ResultCode::C0_OK), $customerInfoResponse->getResultCode());
		$this->assertSame('OK', $customerInfoResponse->getResultMessage());
	}

}
