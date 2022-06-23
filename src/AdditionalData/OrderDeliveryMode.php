<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\AdditionalData;

enum OrderDeliveryMode: string
{

	case ELECTRONIC = '0';
	case SAME_DAY = '1';
	case NEXT_DAY = '2';
	case TWO_OR_MORE_DAYS = '3';

}
