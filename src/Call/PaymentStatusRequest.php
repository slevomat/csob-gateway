<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Api\ApiClientInterface;
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

	public function send(ApiClientInterface $apiClient): PaymentStatusResponse
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
			new SignatureDataFormatter(PaymentStatusResponse::encodeForSignature()),
			null,
			$this->extensions,
		);

		/** @var mixed[] $data */
		$data = $response->getData();
		$data['extensions'] = $response->getExtensions();

		return PaymentStatusResponse::createFromResponseData($data);
	}

	public function registerExtension(string $name, ResponseExtensionHandler $extensionHandler): void
	{
		$this->extensions[$name] = $extensionHandler;
	}

}
