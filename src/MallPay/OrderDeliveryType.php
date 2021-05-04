<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use Consistence\Enum\Enum;

class OrderDeliveryType extends Enum
{

	public const DELIVERY_CARRIER = 'DELIVERY_CARRIER';
	public const PERSONAL_BRANCH = 'PERSONAL_BRANCH';
	public const PERSONAL_PARTNER = 'PERSONAL_PARTNER';
	public const ONLINE = 'ONLINE';

}
