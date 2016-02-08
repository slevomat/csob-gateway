<?php

namespace SlevomatCsobGateway\Api;

use SlevomatCsobGateway\Type\Enum;

class ResponseCode extends Enum
{

	const S200_OK = 200;

	const S303_SEE_OTHER = 303;

	const S400_BAD_REQUEST = 400;
	const S403_FORBIDDEN = 403;
	const S404_NOT_FOUND = 404;
	const S405_METHOD_NOT_ALLOWED = 405;
	const S429_TOO_MANY_REQUESTS = 429;

	const S500_INTERNAL_ERROR = 500;
	const S502_BAD_GATEWAY = 502;
	const S503_SERVICE_UNAVAILABLE = 503;

}
