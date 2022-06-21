<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Extension;

use DateTimeImmutable;
use SlevomatCsobGateway\Call\ResponseExtensionHandler;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;
use function array_flip;

class MaskedCardNumberExtension implements ResponseExtensionHandler
{

	public const NAME = 'maskClnRP';

	/**
	 * @param mixed[] $data
	 */
	public function createResponse(array $data): MaskedCardNumberResponse
	{
		return new MaskedCardNumberResponse(
			$data['longMaskedCln'],
			$data['maskedCln'],
			$this->parseExpiration($data['expiration']),
		);
	}

	public function getSignatureDataFormatter(): SignatureDataFormatter
	{
		return new SignatureDataFormatter(array_flip(['extension', 'dttm', 'maskedCln', 'expiration', 'longMaskedCln']));
	}

	private function parseExpiration(string $expiration): DateTimeImmutable
	{
		return DateTimeImmutable::createFromFormat('d/m/y', '01/' . $expiration)->modify('last day of this month today');
	}

}
