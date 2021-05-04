<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use Consistence\Enum\Enum;

class LogisticsEvent extends Enum
{

	public const DELIVERED = 'delivered';
	public const SENT = 'sent';

}
