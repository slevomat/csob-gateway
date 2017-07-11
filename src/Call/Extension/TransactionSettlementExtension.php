<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\Extension;

use DateTimeImmutable;
use SlevomatCsobGateway\Call\ResponseExtensionHandler;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;

class TransactionSettlementExtension implements ResponseExtensionHandler
{

	const NAME = 'trxDates';

	/**
	 * @param mixed[] $data
	 * @return \SlevomatCsobGateway\Call\Extension\TransactionSettlementResponse
	 */
	public function createResponse(array $data): TransactionSettlementResponse
	{
		return new TransactionSettlementResponse(
			new DateTimeImmutable($data['createdDate']),
			isset($data['authDate']) ? $this->parseAuthDate($data['authDate']) : null,
			isset($data['settlementDate']) ? $this->parseSettlementDate($data['settlementDate']) : null
		);
	}

	public function getSignatureDataFormatter(): SignatureDataFormatter
	{
		return new SignatureDataFormatter(array_flip(['extension', 'dttm', 'createdDate', 'authDate', 'settlementDate']));
	}

	private function parseAuthDate(string $authDate): DateTimeImmutable
	{
		return DateTimeImmutable::createFromFormat('ymdHis', $authDate);
	}

	private function parseSettlementDate(string $settlementDate): DateTimeImmutable
	{
		return DateTimeImmutable::createFromFormat('Ymd', $settlementDate)->setTime(0, 0, 0);
	}

}
