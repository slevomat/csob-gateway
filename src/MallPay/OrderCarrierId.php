<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use Consistence\Enum\Enum;

class OrderCarrierId extends Enum
{

	public const CZ_POST_HAND = 'CZ_POST_HAND';
	public const CZ_POST_OFFICE = 'CZ_POST_OFFICE';
	public const CZ_POST_OTHER = 'CZ_POST_OTHER';
	public const PPL = 'PPL';
	public const DPD = 'DPD';
	public const GEIS = 'GEIS';
	public const IN_TIME = 'IN_TIME';
	public const TOP_TRANS = 'TOP_TRANS';
	public const GEBRUDER_WEISS = 'GEBRUDER_WEISS';
	public const LOCAL_COURIER = 'LOCAL_COURIER';
	public const TNT = 'TNT';
	public const GLS = 'GLS';
	public const HDS_COMFORT = 'HDS_COMFORT';
	public const HDS_STANDARD = 'HDS_STANDARD';
	public const MALL_DEPOSIT = 'MALL_DEPOSIT';

}
