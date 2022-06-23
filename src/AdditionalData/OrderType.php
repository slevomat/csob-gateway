<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\AdditionalData;

enum OrderType: string
{

	case PURCHASE = 'purchase';
	case BALANCE = 'balance';
	case PREPAID = 'prepaid';
	case CASH = 'cash';
	case CHECK = 'check';

}
