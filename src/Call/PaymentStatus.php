<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

use SlevomatCsobGateway\Type\Enum;

class PaymentStatus extends Enum
{

	const S1_CREATED = 1;
	const S2_IN_PROGRESS = 2;
	const S3_CANCELED = 3;
	const S4_CONFIRMED = 4;
	const S5_REVOKED = 5;
	const S6_REJECTED = 6;
	const S7_AWAITING_SETTLEMENT = 7;
	const S8_CHARGED = 8;
	const S9_PROCESSING_REFUND = 9;
	const S10_PAYMENT_REFUNDED = 10;

}
