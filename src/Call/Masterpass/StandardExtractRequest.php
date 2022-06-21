<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Masterpass;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Call\PaymentStatus;
use SlevomatCsobGateway\Call\ResultCode;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;

class StandardExtractRequest
{

	/**
	 * @param mixed[] $callbackParams
	 */
	public function __construct(
		private string $merchantId,
		private string $payId,
		private array $callbackParams,
	)
	{
		Validator::checkPayId($payId);
	}

	public function send(ApiClient $apiClient): ExtractResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'payId' => $this->payId,
			'callbackParams' => $this->callbackParams,
		];

		$response = $apiClient->post(
			'masterpass/standard/extract',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'payId' => null,
				'dttm' => null,
				'callbackParams' => [
					'mpstatus' => null,
					'oauthToken' => null,
					'checkoutResourceUrl' => null,
					'oauthVerifier' => null,
				],
			]),
			new SignatureDataFormatter([
				'payId' => null,
				'dttm' => null,
				'resultCode' => null,
				'resultMessage' => null,
				'paymentStatus' => null,
				'checkoutParams' => [
					'card' => [
						'maskedCln' => null,
						'expiration' => null,
						'billingAddress' => [
							'city' => null,
							'country' => null,
							'countrySubdivision' => null,
							'line1' => null,
							'line2' => null,
							'line3' => null,
							'postalCode' => null,
						],
					],
					'shippingAddress' => [
						'recipientName' => null,
						'recipientPhoneNumber' => null,
						'city' => null,
						'country' => null,
						'countrySubdivision' => null,
						'line1' => null,
						'line2' => null,
						'line3' => null,
						'postalCode' => null,
					],
					'contact' => [
						'firstName' => null,
						'middleName' => null,
						'lastName' => null,
						'country' => null,
						'emailAddress' => null,
						'phoneNumber' => null,
					],
					'rewardProgram' => [
						'rewardNumber' => null,
						'rewardId' => null,
						'rewardName' => null,
						'expiration' => null,
					],
				],
			]),
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return new ExtractResponse(
			$data['payId'],
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			ResultCode::from($data['resultCode']),
			$data['resultMessage'],
			isset($data['paymentStatus']) ? PaymentStatus::from($data['paymentStatus']) : null,
			$data['checkoutParams'] ?? null,
		);
	}

}
