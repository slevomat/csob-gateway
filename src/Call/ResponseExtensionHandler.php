<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Crypto\SignatureDataFormatter;

interface ResponseExtensionHandler
{

	/**
	 * @param array $decodeData
	 * @return mixed
	 */
	public function createResponse(array $decodeData);

	public function getSignatureDataFormatter(): SignatureDataFormatter;

}
