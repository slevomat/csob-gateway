<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

enum PayOperation: string
{

	case PAYMENT = 'payment';
	case ONECLICK_PAYMENT = 'oneclickPayment';
	case CUSTOM_PAYMENT = 'customPayment';

}
