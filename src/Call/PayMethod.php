<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call;

enum PayMethod: string
{

	case CARD = 'card';

	case CARD_LVP = 'card#LVP';

}
