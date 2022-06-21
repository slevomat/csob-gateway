<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

enum OrderItemType: string
{

	case PHYSICAL = 'PHYSICAL';
	case DISCOUNT = 'DISCOUNT';
	case DIGITAL = 'DIGITAL';
	case GIFT_CARD = 'GIFT_CARD';
	case STORE_CREDIT = 'STORE_CREDIT';
	case SALES_TAX = 'SALES_TAX';
	case SHIPPING_FEE = 'SHIPPING_FEE';
	case INSURANCE = 'INSURANCE';
	case FEE = 'FEE';

}
