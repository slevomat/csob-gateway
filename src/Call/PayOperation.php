<?php

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Type\Enum;

class PayOperation extends Enum
{

	const PAYMENT = 'payment';
	const RECURRENT_PAYMENT = 'recurrentPayment';

}
