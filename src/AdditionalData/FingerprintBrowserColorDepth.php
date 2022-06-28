<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\AdditionalData;

enum FingerprintBrowserColorDepth : int
{

	case DEPTH_1 = 1;
	case DEPTH_4 = 4;
	case DEPTH_8 = 8;
	case DEPTH_15 = 15;
	case DEPTH_16 = 16;
	case DEPTH_24 = 24;
	case DEPTH_32 = 32;
	case DEPTH_48 = 48;

}
