<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

enum ResponseCode: int
{

	case S200_OK = 200;

	case S303_SEE_OTHER = 303;

	case S400_BAD_REQUEST = 400;
	case S403_FORBIDDEN = 403;
	case S404_NOT_FOUND = 404;
	case S405_METHOD_NOT_ALLOWED = 405;
	case S429_TOO_MANY_REQUESTS = 429;

	case S500_INTERNAL_ERROR = 500;
	case S502_BAD_GATEWAY = 502;
	case S503_SERVICE_UNAVAILABLE = 503;
	case S504_GATEWAY_TIMEOUT = 504;

}
