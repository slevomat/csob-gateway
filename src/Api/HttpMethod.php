<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

use SlevomatCsobGateway\Type\Enum;

class HttpMethod extends Enum
{

	const GET = 'GET';
	const POST = 'POST';
	const PUT = 'PUT';

}
