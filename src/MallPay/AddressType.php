<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

enum AddressType: string
{

	case DELIVERY = 'DELIVERY';
	case BILLING = 'BILLING';

}
