<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\AdditionalData;

enum CustomerLoginAuth: string
{

	case GUEST = 'guest';
	case ACCOUNT = 'account';
	case FEDERATED = 'federated';
	case ISSUER = 'issuer';
	case THIRD_PARTY = 'thirdparty';
	case FIDO = 'fido';
	case FIDO_SIGNED = 'fido_signed';
	case API = 'api';

}
