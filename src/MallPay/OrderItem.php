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

	/** @var string */
	private $code;

	/** @var string|null */
	private $ean;

	/** @var string */
	private $name;

	/** @var OrderItemType|null */
	private $type;

	/** @var int|null */
	private $quantity;

	/** @var string|null */
	private $variant;

	/** @var string|null */
	private $description;

	/** @var string|null */
	private $producer;

	/** @var string[]|null */
	private $categories;

	/** @var Price|null */
	private $unitPrice;

	/** @var Vat|null */
	private $unitVat;

	/** @var Price */
	private $totalPrice;

	/** @var Vat */
	private $totalVat;

	/** @var string|null */
	private $productUrl;

	/**
	 * @param string $code
	 * @param string|null $ean
	 * @param string $name
	 * @param OrderItemType|null $type
	 * @param int|null $quantity
	 * @param string|null $variant
	 * @param string|null $description
	 * @param string|null $producer
	 * @param string[]|null $categories
	 * @param Price|null $unitPrice
	 * @param Vat|null $unitVat
	 * @param Price $totalPrice
	 * @param Vat $totalVat
	 * @param string|null $productUrl
	 */
	public function __construct(
		string $code,
		?string $ean,
		string $name,
		?OrderItemType $type,
		?int $quantity,
		?string $variant,
		?string $description,
		?string $producer,
		?array $categories,
		?Price $unitPrice,
		?Vat $unitVat,
		Price $totalPrice,
		Vat $totalVat,
		?string $productUrl
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

		$this->code = $code;
		$this->ean = $ean;
		$this->name = $name;
		$this->type = $type;
		$this->quantity = $quantity;
		$this->variant = $variant;
		$this->description = $description;
		$this->producer = $producer;
		$this->categories = $categories;
		$this->unitPrice = $unitPrice;
		$this->unitVat = $unitVat;
		$this->totalPrice = $totalPrice;
		$this->totalVat = $totalVat;
		$this->productUrl = $productUrl;
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
			$data['type'] = $this->type->getValue();
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
