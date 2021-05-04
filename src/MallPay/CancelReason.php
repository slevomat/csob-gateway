<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use Consistence\Enum\Enum;

class CancelReason extends Enum
{

	public const ABORTED = 'aborted';
	public const OTHER_PAYMENT = 'other_payment';
	public const UNDELIVERABLE = 'undeliverable';
	public const UNAVAILABLE = 'unavailable';
	public const ABANDONED = 'abandoned';
	public const CHANGED = 'changed';
	public const UNPROCESSED = 'unprocessed';

}
