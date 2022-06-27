<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use DateTimeImmutable;
use SlevomatCsobGateway\Api\HttpMethod;
use function array_key_exists;
use function array_merge;

class ActionsPaymentResponse extends StatusDetailPaymentResponse
{

	public function __construct(
		string $payId,
		DateTimeImmutable $responseDateTime,
		ResultCode $resultCode,
		string $resultMessage,
		?PaymentStatus $paymentStatus = null,
		?string $statusDetail = null,
		private ?Actions $actions = null,
	)
	{
		parent::__construct($payId, $responseDateTime, $resultCode, $resultMessage, $paymentStatus, $statusDetail);
	}

	/**
	 * @param mixed[] $data
	 */
	public static function createFromResponseData(array $data): self
	{
		$paymentResponse = parent::createFromResponseData($data);

		return new self(
			$paymentResponse->getPayId(),
			$paymentResponse->getResponseDateTime(),
			$paymentResponse->getResultCode(),
			$paymentResponse->getResultMessage(),
			$paymentResponse->getPaymentStatus(),
			$paymentResponse->getStatusDetail(),
			array_key_exists('actions', $data) ? new Actions(
				array_key_exists('fingerprint', $data['actions']) ? new ActionsFingerprint(
					array_key_exists('browserInit', $data['actions']['fingerprint']) ? new ActionsEndpoint(
						$data['actions']['fingerprint']['browserInit']['url'],
						array_key_exists('method', $data['actions']['fingerprint']['browserInit']) ? HttpMethod::from($data['actions']['fingerprint']['browserInit']['method']) : null,
						$data['actions']['fingerprint']['browserInit']['vars'] ?? null,
					) : null,
					array_key_exists('sdkInit', $data['actions']['fingerprint']) ? new ActionsFingerprintSdkInit(
						$data['actions']['fingerprint']['sdkInit']['directoryServerID'],
						$data['actions']['fingerprint']['sdkInit']['schemeId'],
						$data['actions']['fingerprint']['sdkInit']['messageVersion'],
					) : null,
				) : null,
				array_key_exists('authenticate', $data['actions']) ? new ActionsAuthenticate(
					array_key_exists('browserChallenge', $data['actions']['authenticate']) ? new ActionsEndpoint(
						$data['actions']['authenticate']['browserChallenge']['url'],
						array_key_exists('method', $data['actions']['authenticate']['browserChallenge']) ? HttpMethod::from($data['actions']['authenticate']['browserChallenge']['method']) : null,
						$data['actions']['authenticate']['browserChallenge']['vars'] ?? null,
					) : null,
					array_key_exists('sdkChallenge', $data['actions']['authenticate']) ? new ActionsAuthenticateSdkChallenge(
						$data['actions']['authenticate']['sdkChallenge']['threeDSServerTransID'],
						$data['actions']['authenticate']['sdkChallenge']['acsReferenceNumber'],
						$data['actions']['authenticate']['sdkChallenge']['acsTransID'],
						$data['actions']['authenticate']['sdkChallenge']['acsSignedContent'],
					) : null,
				) : null,
			) : null,
		);
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return array_merge(parent::encodeForSignature(), [
			'actions' => Actions::encodeForSignature(),
		]);
	}

	public function getActions(): ?Actions
	{
		return $this->actions;
	}

}
