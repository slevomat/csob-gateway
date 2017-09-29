<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

class ResultCode extends \Consistence\Enum\Enum
{

	public const C0_OK = 0;

	public const C100_MISSING_PARAMETER = 100;
	public const C110_INVALID_PARAMETER = 110;
	public const C120_MERCHANT_BLOCKED = 120;
	public const C130_SESSION_EXPIRED = 130;
	public const C140_PAYMENT_NOT_FOUND = 140;
	public const C150_PAYMENT_NOT_IN_VALID_STATE = 150;
	public const C180_OPERATION_NOT_ALLOWED = 180;

	public const C800_CUSTOMER_NOT_FOUND = 800;
	public const C810_CUSTOMER_FOUND_NOT_SAVED_CARD = 810;
	public const C820_CUSTOMER_FOUND_WITH_SAVED_CARD = 820;

	public const C900_INTERNAL_ERROR = 900;
	public const C999_GENERAL_ERROR = 999;

}
