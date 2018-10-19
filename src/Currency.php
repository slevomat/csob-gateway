<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

use Consistence\Enum\Enum;

class Currency extends Enum
{

	public const CZK = 'CZK';
	public const EUR = 'EUR';
	public const USD = 'USD';
	public const GBP = 'GBP';
	public const HRK = 'HRK';

}
