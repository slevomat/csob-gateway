<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\GooglePay;

enum InitParamsEnvironment : string
{

	case TEST = 'TEST';
	case PRODUCTION = 'PRODUCTION';

}
