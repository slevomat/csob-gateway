<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

enum OrderDeliveryType: string
{

	case DELIVERY_CARRIER = 'DELIVERY_CARRIER';
	case PERSONAL_BRANCH = 'PERSONAL_BRANCH';
	case PERSONAL_PARTNER = 'PERSONAL_PARTNER';
	case ONLINE = 'ONLINE';

}
