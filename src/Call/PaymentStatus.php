<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

enum PaymentStatus: int
{

	case S0_ERROR = 0;
	case S1_CREATED = 1;
	case S2_IN_PROGRESS = 2;
	case S3_CANCELED = 3;
	case S4_CONFIRMED = 4;
	case S5_REVOKED = 5;
	case S6_REJECTED = 6;
	case S7_AWAITING_SETTLEMENT = 7;
	case S8_CHARGED = 8;
	case S9_PROCESSING_REFUND = 9;
	case S10_PAYMENT_REFUNDED = 10;

}
