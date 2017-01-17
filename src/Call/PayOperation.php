<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

class PayOperation extends \Consistence\Enum\Enum
{

	const PAYMENT = 'payment';
	const ONECLICK_PAYMENT = 'oneclickPayment';

}
