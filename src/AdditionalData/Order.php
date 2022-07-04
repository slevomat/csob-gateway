<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\AdditionalData;

use DateTimeImmutable;
use InvalidArgumentException;
use SlevomatCsobGateway\Encodable;
use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\Validator;
use function array_filter;
use const DATE_ATOM;

class Order implements Encodable
{

	public const EMAIL_LENGTH_MAX = 100;

	public function __construct(
		private ?OrderType $type = null,
		private ?OrderAvailability $availability = null,
		private ?DateTimeImmutable $availabilityDate = null,
		private ?OrderDelivery $delivery = null,
		private ?OrderDeliveryMode $deliveryMode = null,
		private ?string $deliveryEmail = null,
		private ?bool $nameMatch = null,
		private ?bool $addressMatch = null,
		private ?OrderAddress $billing = null,
		private ?OrderAddress $shipping = null,
		private ?DateTimeImmutable $shippingAddedAt = null,
		private ?bool $reorder = null,
		private ?OrderGiftcards $giftcards = null,
	)
	{
		if ($this->availability === OrderAvailability::DATE xor $this->availabilityDate !== null) {
			throw new InvalidArgumentException('If $availability is set to DATE, $availabilityDate must be provided.');
		}

		if ($this->deliveryEmail !== null) {
			Validator::checkWhitespacesAndLength($this->deliveryEmail, self::EMAIL_LENGTH_MAX);
			Validator::checkEmail($this->deliveryEmail);
		}
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'type' => $this->type?->value,
			'availability' => $this->availability === OrderAvailability::DATE ? $this->availabilityDate?->format(DATE_ATOM) : $this->availability?->value,
			'delivery' => $this->delivery?->value,
			'deliveryMode' => $this->deliveryMode?->value,
			'deliveryEmail' => $this->deliveryEmail,
			'nameMatch' => $this->nameMatch,
			'addressMatch' => $this->addressMatch,
			'billing' => $this->billing?->encode(),
			'shipping' => $this->shipping?->encode(),
			'shippingAddedAt' => $this->shippingAddedAt?->format(DATE_ATOM),
			'reorder' => $this->reorder,
			'giftcards' => $this->giftcards?->encode(),
		], EncodeHelper::filterValueCallback());
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'type' => null,
			'availability' => null,
			'delivery' => null,
			'deliveryMode' => null,
			'deliveryEmail' => null,
			'nameMatch' => null,
			'addressMatch' => null,
			'billing' => OrderAddress::encodeForSignature(),
			'shipping' => OrderAddress::encodeForSignature(),
			'shippingAddedAt' => null,
			'reorder' => null,
			'giftcards' => OrderGiftcards::encodeForSignature(),
		];
	}

}
