<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use Consistence\Enum\Enum;

class OrderItemType extends Enum
{

	public const PHYSICAL = 'PHYSICAL';
	public const DISCOUNT = 'DISCOUNT';
	public const DIGITAL = 'DIGITAL';
	public const GIFT_CARD = 'GIFT_CARD';
	public const STORE_CREDIT = 'STORE_CREDIT';
	public const SALES_TAX = 'SALES_TAX';
	public const SHIPPING_FEE = 'SHIPPING_FEE';
	public const INSURANCE = 'INSURANCE';
	public const FEE = 'FEE';

}
