<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Type\Enum;

class ResultCode extends Enum
{

	const C0_OK = 0;

	const C100_MISSING_PARAMETER = 100;
	const C110_INVALID_PARAMETER = 110;
	const C120_MERCHANT_BLOCKED = 120;
	const C130_SESSION_EXPIRED = 130;
	const C140_PAYMENT_NOT_FOUND = 140;
	const C150_PAYMENT_NOT_IN_VALID_STATE = 150;
	const C180_OPERATION_NOT_ALLOWED = 180;

	const C800_CUSTOMER_NOT_FOUND = 800;
	const C810_CUSTOMER_FOUND_NOT_SAVED_CARD = 810;
	const C820_CUSTOMER_FOUND_WITH_SAVED_CARD = 820;

	const C900_INTERNAL_ERROR = 900;
	const C999_GENERAL_ERROR = 999;

}
