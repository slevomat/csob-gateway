<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Type\Enum;

class PayOperation extends Enum
{

	const PAYMENT = 'payment';
	const ONECLICK_PAYMENT = 'oneclickPayment';

}
