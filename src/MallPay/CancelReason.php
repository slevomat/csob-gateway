<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

enum CancelReason: string
{

	case ABORTED = 'aborted';
	case OTHER_PAYMENT = 'other_payment';
	case UNDELIVERABLE = 'undeliverable';
	case UNAVAILABLE = 'unavailable';
	case ABANDONED = 'abandoned';
	case CHANGED = 'changed';
	case UNPROCESSED = 'unprocessed';

}
