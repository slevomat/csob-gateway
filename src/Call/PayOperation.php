<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use Consistence\Enum\Enum;

class PayOperation extends Enum
{

	public const PAYMENT = 'payment';
	public const ONECLICK_PAYMENT = 'oneclickPayment';
	public const CUSTOM_PAYMENT = 'customPayment';

}
