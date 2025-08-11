<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use InvalidArgumentException;
use SlevomatCsobGateway\Api\ApiClientInterface;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use function array_key_exists;
use function in_array;
use function is_numeric;
use function sprintf;

class ReceivePaymentRequest
{

	/**
	 * @param mixed[] $data
	 */
	public function send(ApiClientInterface $apiClient, array $data): ReceivePaymentResponse
	{
		$allowedFields = ['payId', 'dttm', 'resultCode', 'resultMessage', 'paymentStatus', 'authCode', 'merchantData', 'statusDetail', 'signature'];
		$optionalFields = ['paymentStatus', 'authCode', 'merchantData', 'statusDetail'];

		$validated = [];

		foreach ($allowedFields as $key) {
			if (!isset($data[$key]) && !in_array($key, $optionalFields, true)) {
				throw new InvalidArgumentException(sprintf('Missing parameter %s in gateway response', $key));
			}
			if (isset($data[$key])) {
				$validated[$key] = $data[$key];
			}
		}

		if (array_key_exists('resultCode', $validated) && is_numeric($validated['resultCode'])) {
			$validated['resultCode'] = (int) $validated['resultCode'];
		}

		if (array_key_exists('paymentStatus', $validated) && is_numeric($validated['paymentStatus'])) {
			$validated['paymentStatus'] = (int) $validated['paymentStatus'];
		}

		$response = $apiClient->createResponseByData($validated, new SignatureDataFormatter(ReceivePaymentResponse::encodeForSignature()));

		/** @var mixed[] $data */
		$data = $response->getData();

		return ReceivePaymentResponse::createFromResponseData($data);
	}

}
