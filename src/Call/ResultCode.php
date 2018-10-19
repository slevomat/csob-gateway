<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use Consistence\Enum\Enum;

class ResultCode extends Enum
{

	public const C0_OK = 0;

	public const C100_MISSING_PARAMETER = 100;
	public const C110_INVALID_PARAMETER = 110;
	public const C120_MERCHANT_BLOCKED = 120;
	public const C130_SESSION_EXPIRED = 130;
	public const C140_PAYMENT_NOT_FOUND = 140;
	public const C150_PAYMENT_NOT_IN_VALID_STATE = 150;
	public const C180_OPERATION_NOT_ALLOWED = 180;

	public const C220_MPASS_AT_SHOP_DISABLED = 220;
	public const C230_MPASS_NOT_ONBOARDED = 230;
	public const C240_MPASS_TOKEN_ALREADY_INITIALIZED = 240;
	public const C250_MPASS_TOKEN_DOES_NOT_EXISTS = 250;
	public const C260_MPASS_SERVER_ERROR = 260;
	public const C270_MPASS_CANCELLED_BY_USER = 270;

	public const C400_CSOB_BUTTON_DISABLED = 400;
	public const C410_ERA_BUTTON_DISABLED = 410;
	public const C420_CSOB_BUTTON_UNAVAILABLE = 420;
	public const C430_ERA_BUTTON_UNAVAILABLE = 430;

	public const C500_EET_REJECTED = 500;

	public const C800_CUSTOMER_NOT_FOUND = 800;
	public const C810_CUSTOMER_FOUND_NOT_SAVED_CARD = 810;
	public const C820_CUSTOMER_FOUND_WITH_SAVED_CARD = 820;

	public const C900_INTERNAL_ERROR = 900;
	public const C999_GENERAL_ERROR = 999;

}
