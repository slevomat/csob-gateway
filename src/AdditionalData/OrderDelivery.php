<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\AdditionalData;

enum OrderDelivery: string
{

	case SHIPPING = 'shipping';
	case SHIPPING_VERIFIED = 'shipping_verified';
	case IN_STORE = 'instore';
	case DIGITAL = 'digital';
	case TICKET = 'ticket';
	case OTHER = 'other';

}
