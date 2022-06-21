<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

enum OrderCarrierId: string
{

	case CZ_POST_HAND = 'CZ_POST_HAND';
	case CZ_POST_OFFICE = 'CZ_POST_OFFICE';
	case CZ_POST_OTHER = 'CZ_POST_OTHER';
	case PPL = 'PPL';
	case DPD = 'DPD';
	case GEIS = 'GEIS';
	case IN_TIME = 'IN_TIME';
	case TOP_TRANS = 'TOP_TRANS';
	case GEBRUDER_WEISS = 'GEBRUDER_WEISS';
	case LOCAL_COURIER = 'LOCAL_COURIER';
	case TNT = 'TNT';
	case GLS = 'GLS';
	case HDS_COMFORT = 'HDS_COMFORT';
	case HDS_STANDARD = 'HDS_STANDARD';
	case MALL_DEPOSIT = 'MALL_DEPOSIT';

}
