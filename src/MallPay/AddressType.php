<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use Consistence\Enum\Enum;

class AddressType extends Enum
{

	public const DELIVERY = 'DELIVERY';
	public const BILLING = 'BILLING';

}
