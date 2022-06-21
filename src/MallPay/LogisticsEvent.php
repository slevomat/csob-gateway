<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

enum LogisticsEvent: string
{

	case DELIVERED = 'delivered';
	case SENT = 'sent';

}
