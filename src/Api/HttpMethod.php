<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

use Consistence\Enum\Enum;

class HttpMethod extends Enum
{

	public const GET = 'GET';
	public const POST = 'POST';
	public const PUT = 'PUT';

}
