<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use SlevomatCsobGateway\Validator;

class PaymentStatusRequest
{

	/** @var ResponseExtensionHandler[] */
	private array $extensions = [];

	public function __construct(private string $merchantId, private string $payId)
	{
		Validator::checkPayId($payId);
	}

	public function send(ApiClient $apiClient): PaymentResponse
	{
		$response = $apiClient->get(
			'payment/status/{merchantId}/{payId}/{dttm}/{signature}',
			[
				'merchantId' => $this->merchantId,
				'payId' => $this->payId,
			],
			new SignatureDataFormatter([
				'merchantId' => null,
				'payId' => null,
				'dttm' => null,
			]),
			new SignatureDataFormatter([
				'payId' => null,
				'dttm' => null,
				'resultCode' => null,
				'resultMessage' => null,
				'paymentStatus' => null,
				'authCode' => null,
				'statusDetail' => null,
			]),
			null,
			$this->extensions,
		);

		/** @var mixed[] $data */
		$data = $response->getData();

		return new PaymentResponse(
			$data['payId'],
			DateTimeImmutable::createFromFormat('YmdHis', $data['dttm']),
			ResultCode::from($data['resultCode']),
			$data['resultMessage'],
			isset($data['paymentStatus']) ? PaymentStatus::from($data['paymentStatus']) : null,
			$data['authCode'] ?? null,
			null,
			$response->getExtensions(),
			$data['statusDetail'] ?? null,
		);
	}

	public function registerExtension(string $name, ResponseExtensionHandler $extensionHandler): void
	{
		$this->extensions[$name] = $extensionHandler;
	}

}
