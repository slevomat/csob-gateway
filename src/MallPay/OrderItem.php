<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\Price;
use SlevomatCsobGateway\Validator;
use function array_filter;

class OrderItem
{

	public const CODE_VARIANT_PRODUCER_LENGTH_MAX = 50;
	public const EAN_LENGTH_MAX = 15;
	public const NAME_LENGTH_MAX = 200;
	public const DESCRIPTION_LENGTH_MAX = 100;
	public const PRODUCT_URL_LENGTH_MAX = 250;

	/**
	 * @param string[]|null $categories
	 */
	public function __construct(
		private string $code,
		private ?string $ean,
		private string $name,
		private ?OrderItemType $type,
		private ?int $quantity,
		private ?string $variant,
		private ?string $description,
		private ?string $producer,
		private ?array $categories,
		private ?Price $unitPrice,
		private ?Vat $unitVat,
		private Price $totalPrice,
		private Vat $totalVat,
		private ?string $productUrl = null,
	)
	{
		Validator::checkWhitespacesAndLength($code, self::CODE_VARIANT_PRODUCER_LENGTH_MAX);
		Validator::checkWhitespacesAndLength($name, self::NAME_LENGTH_MAX);
		if ($ean !== null) {
			Validator::checkWhitespacesAndLength($ean, self::EAN_LENGTH_MAX);
		}
		if ($quantity !== null) {
			Validator::checkNumberPositive($quantity);
		}
		if ($variant !== null) {
			Validator::checkWhitespacesAndLength($variant, self::CODE_VARIANT_PRODUCER_LENGTH_MAX);
		}
		if ($description !== null) {
			Validator::checkWhitespacesAndLength($description, self::DESCRIPTION_LENGTH_MAX);
		}
		if ($producer !== null) {
			Validator::checkWhitespacesAndLength($producer, self::CODE_VARIANT_PRODUCER_LENGTH_MAX);
		}
		if ($productUrl !== null) {
			Validator::checkUrl($productUrl);
			Validator::checkWhitespacesAndLength($productUrl, self::PRODUCT_URL_LENGTH_MAX);
		}
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'code' => $this->code,
			'ean' => $this->ean,
			'name' => $this->name,
			'type' => $this->type?->value,
			'quantity' => $this->quantity,
			'variant' => $this->variant,
			'description' => $this->description,
			'producer' => $this->producer,
			'categories' => $this->categories,
			'unitPrice' => $this->unitPrice?->encode(),
			'unitVat' => $this->unitVat?->encode(),
			'totalPrice' => $this->totalPrice->encode(),
			'totalVat' => $this->totalVat->encode(),
			'productUrl' => $this->productUrl,
		], EncodeHelper::filterValueCallback());
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'code' => null,
			'ean' => null,
			'name' => null,
			'type' => null,
			'quantity' => null,
			'variant' => null,
			'description' => null,
			'producer' => null,
			'categories' => [],
			'unitPrice' => Price::encodeForSignature(),
			'unitVat' => Vat::encodeForSignature(),
			'totalPrice' => Price::encodeForSignature(),
			'totalVat' => Vat::encodeForSignature(),
			'productUrl' => null,
		];
	}

	public function getCode(): string
	{
		return $this->code;
	}

	public function getEan(): ?string
	{
		return $this->ean;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getType(): ?OrderItemType
	{
		return $this->type;
	}

	public function getQuantity(): ?int
	{
		return $this->quantity;
	}

	public function getVariant(): ?string
	{
		return $this->variant;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function getProducer(): ?string
	{
		return $this->producer;
	}

	/**
	 * @return string[]|null
	 */
	public function getCategories(): ?array
	{
		return $this->categories;
	}

	public function getUnitPrice(): ?Price
	{
		return $this->unitPrice;
	}

	public function getUnitVat(): ?Vat
	{
		return $this->unitVat;
	}

	public function getTotalPrice(): Price
	{
		return $this->totalPrice;
	}

	public function getTotalVat(): Vat
	{
		return $this->totalVat;
	}

	public function getProductUrl(): ?string
	{
		return $this->productUrl;
	}

}
