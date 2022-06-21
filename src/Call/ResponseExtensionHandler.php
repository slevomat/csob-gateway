<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Crypto\SignatureDataFormatter;

interface ResponseExtensionHandler
{

	/**
	 * @param mixed[] $decodeData
	 */
	public function createResponse(array $decodeData): mixed;

	public function getSignatureDataFormatter(): SignatureDataFormatter;

}
