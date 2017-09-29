<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

class PayOperation extends \Consistence\Enum\Enum
{

	public const PAYMENT = 'payment';
	public const ONECLICK_PAYMENT = 'oneclickPayment';

}
