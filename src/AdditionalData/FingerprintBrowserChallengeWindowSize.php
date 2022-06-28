<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\AdditionalData;

enum FingerprintBrowserChallengeWindowSize : string
{

	case SIZE_01_250_X_400 = '01';
	case SIZE_02_390_X_400 = '02';
	case SIZE_03_500_X_600 = '03';
	case SIZE_04_600_X_400 = '04';
	case SIZE_05_FULL_SCREEN = '05';

}
