<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

class ResponseCode extends \Consistence\Enum\Enum
{

	public const S200_OK = 200;

	public const S303_SEE_OTHER = 303;

	public const S400_BAD_REQUEST = 400;
	public const S403_FORBIDDEN = 403;
	public const S404_NOT_FOUND = 404;
	public const S405_METHOD_NOT_ALLOWED = 405;
	public const S429_TOO_MANY_REQUESTS = 429;

	public const S500_INTERNAL_ERROR = 500;
	public const S502_BAD_GATEWAY = 502;
	public const S503_SERVICE_UNAVAILABLE = 503;
	public const S504_GATEWAY_TIMEOUT = 504;

}
