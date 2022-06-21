<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\MallPay;

use SlevomatCsobGateway\Price;
use SlevomatCsobGateway\Validator;

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
		$data = [
			'code' => $this->code,
			'name' => $this->name,
			'totalPrice' => $this->totalPrice->encode(),
			'totalVat' => $this->totalVat->encode(),
		];

		if ($this->ean !== null) {
			$data['ean'] = $this->ean;
		}
		if ($this->type !== null) {
			$data['type'] = $this->type->value;
		}
		if ($this->quantity !== null) {
			$data['quantity'] = $this->quantity;
		}
		if ($this->variant !== null) {
			$data['variant'] = $this->variant;
		}
		if ($this->description !== null) {
			$data['description'] = $this->description;
		}
		if ($this->producer !== null) {
			$data['producer'] = $this->producer;
		}
		if ($this->categories !== null) {
			$data['categories'] = $this->categories;
		}
		if ($this->unitPrice !== null) {
			$data['unitPrice'] = $this->unitPrice->encode();
		}
		if ($this->unitVat !== null) {
			$data['unitVat'] = $this->unitVat->encode();
		}
		if ($this->productUrl !== null) {
			$data['productUrl'] = $this->productUrl;
		}

		return $data;
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
