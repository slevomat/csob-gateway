<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\AdditionalData;

enum OrderAvailability: string
{

	case NOW = 'now';
	case PREORDER = 'preorder';
	case DATE = 'date';

}
