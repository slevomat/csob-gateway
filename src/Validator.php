<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

use InvalidArgumentException;
use SlevomatCsobGateway\Api\HttpMethod;
use function base64_encode;
use function ctype_digit;
use function filter_var;
use function preg_match;
use function preg_quote;
use function preg_replace;
use function sprintf;
use function strlen;
use function utf8_decode;
use const FILTER_VALIDATE_EMAIL;
use const FILTER_VALIDATE_URL;

class Validator
{

	private const CART_ITEM_NAME_LENGTH_MAX = 20;
	private const CART_ITEM_DESCRIPTION_LENGTH_MAX = 40;

	private const ORDER_ID_LENGTH_MAX = 10;
	private const RETURN_URL_LENGTH_MAX = 300;
	private const DESCRIPTION_LENGTH_MAX = 255;
	private const MERCHANT_DATA_LENGTH_MAX = 255;
	private const CUSTOMER_ID_LENGTH_MAX = 50;
	private const PAY_ID_LENGTH_MAX = 15;

	private const TTL_SEC_MIN = 300;
	private const TTL_SEC_MAX = 1800;

	private const MALL_PAY_TTL_SEC_MIN = 600;
	private const MALL_PAY_TTL_SEC_MAX = 43200;

	public static function checkCartItemName(string $name): void
	{
		self::checkWhitespaces($name);

		if (strlen(utf8_decode($name)) > self::CART_ITEM_NAME_LENGTH_MAX) {
			throw new InvalidArgumentException(sprintf('Cart item name can have maximum of %d characters.', self::CART_ITEM_NAME_LENGTH_MAX));
		}
	}

	public static function checkCartItemDescription(string $description): void
	{
		self::checkWhitespaces($description);

		if (strlen(utf8_decode($description)) > self::CART_ITEM_DESCRIPTION_LENGTH_MAX) {
			throw new InvalidArgumentException(sprintf('Cart item description can have maximum of %d characters.', self::CART_ITEM_DESCRIPTION_LENGTH_MAX));
		}
	}

	public static function checkCartItemQuantity(int $quantity): void
	{
		if ($quantity < 1) {
			throw new InvalidArgumentException(sprintf(
				'Quantity must be greater than 0. %d given.',
				$quantity,
			));
		}
	}

	public static function checkOrderId(string $orderId): void
	{
		self::checkWhitespaces($orderId);

		if (!ctype_digit($orderId)) {
			throw new InvalidArgumentException(sprintf(
				'OrderId must be numeric value. %s given.',
				$orderId,
			));
		}

		if (strlen($orderId) > self::ORDER_ID_LENGTH_MAX) {
			throw new InvalidArgumentException(sprintf('OrderId can have maximum of %d characters.', self::ORDER_ID_LENGTH_MAX));
		}
	}

	public static function checkReturnUrl(string $returnUrl): void
	{
		self::checkWhitespaces($returnUrl);

		if (strlen(utf8_decode($returnUrl)) > self::RETURN_URL_LENGTH_MAX) {
			throw new InvalidArgumentException(sprintf('ReturnUrl can have maximum of %d characters.', self::RETURN_URL_LENGTH_MAX));
		}
	}

	public static function checkReturnMethod(HttpMethod $httpMethod): void
	{
		if ($httpMethod !== HttpMethod::POST && $httpMethod !== HttpMethod::GET) {
			throw new InvalidArgumentException(sprintf('Only %s or %s is allowed as returnMethod.', HttpMethod::POST->value, HttpMethod::GET->value));
		}
	}

	public static function checkDescription(string $description): void
	{
		self::checkWhitespaces($description);

		if (strlen(utf8_decode($description)) > self::DESCRIPTION_LENGTH_MAX) {
			throw new InvalidArgumentException(sprintf('Description can have maximum of %d characters.', self::DESCRIPTION_LENGTH_MAX));
		}
	}

	public static function checkMerchantData(string $merchantData): void
	{
		self::checkWhitespaces($merchantData);

		if (strlen(utf8_decode(base64_encode($merchantData))) > self::MERCHANT_DATA_LENGTH_MAX) {
			throw new InvalidArgumentException(sprintf('MerchantData can have maximum of %d characters in encoded state.', self::MERCHANT_DATA_LENGTH_MAX));
		}
	}

	public static function checkCustomerId(string $customerId): void
	{
		self::checkWhitespaces($customerId);

		if (strlen(utf8_decode($customerId)) > self::CUSTOMER_ID_LENGTH_MAX) {
			throw new InvalidArgumentException(sprintf('CustomerId can have maximum of %d characters.', self::CUSTOMER_ID_LENGTH_MAX));
		}
	}

	public static function checkPayId(string $payId): void
	{
		self::checkWhitespaces($payId);

		if (strlen(utf8_decode($payId)) > self::PAY_ID_LENGTH_MAX) {
			throw new InvalidArgumentException(sprintf('PayId can have maximum of %d characters.', self::PAY_ID_LENGTH_MAX));
		}
	}

	private static function checkWhitespaces(string $argument): void
	{
		$charlist = preg_quote(" \t\n\r\0\x0B\xC2\xA0", '#');
		preg_replace('#^[' . $charlist . ']+|[' . $charlist . ']+\z#u', '', $argument);

		if ($argument !== preg_replace('#^[' . $charlist . ']+|[' . $charlist . ']+\z#u', '', $argument)) {
			throw new InvalidArgumentException('Argument starts or ends with whitespace.');
		}
	}

	public static function checkTtlSec(int $ttlSec): void
	{
		if ($ttlSec < self::TTL_SEC_MIN || $ttlSec > self::TTL_SEC_MAX) {
			throw new InvalidArgumentException(sprintf('TTL sec is out of range (%d - %d). Current value is %d.', self::TTL_SEC_MIN, self::TTL_SEC_MAX, $ttlSec));
		}
	}

	public static function checkMallPayTtlSec(int $ttlSec): void
	{
		if ($ttlSec < self::MALL_PAY_TTL_SEC_MIN || $ttlSec > self::MALL_PAY_TTL_SEC_MAX) {
			throw new InvalidArgumentException(sprintf('TTL sec is out of range (%d - %d). Current value is %d.', self::MALL_PAY_TTL_SEC_MIN, self::MALL_PAY_TTL_SEC_MAX, $ttlSec));
		}
	}

	public static function checkWhitespacesAndLength(string $value, int $maxLength): void
	{
		self::checkWhitespaces($value);

		if (strlen(utf8_decode($value)) > $maxLength) {
			throw new InvalidArgumentException(sprintf('Field must have maximum of %d characters.', $maxLength));
		}
	}

	public static function checkNumberPositiveOrZero(int $value): void
	{
		if ($value < 0) {
			throw new InvalidArgumentException('Value is negative.');
		}
	}

	public static function checkNumberPositive(int $value): void
	{
		if ($value <= 0) {
			throw new InvalidArgumentException('Value is negative or zero.');
		}
	}

	public static function checkNumberRange(int $value, int $min, int $max): void
	{
		if ($value < $min || $value > $max) {
			throw new InvalidArgumentException(sprintf('Value %d is not in range <%d, %d>.', $value, $min, $max));
		}
	}

	public static function checkNumberGraterEqualThen(int $value, int $min): void
	{
		if ($value < $min) {
			throw new InvalidArgumentException(sprintf('Value %d must be >= than %d.', $value, $min));
		}
	}

	public static function checkEmail(string $value): void
	{
		if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
			throw new InvalidArgumentException('E-mail is not valid.');
		}
	}

	public static function checkUrl(string $value): void
	{
		if (filter_var($value, FILTER_VALIDATE_URL) === false) {
			throw new InvalidArgumentException('URL is not valid.');
		}
	}

	public static function checkPhone(string $phone): void
	{
		if (preg_match('~^(\\+|00)?\\d{1,3}\\.( *\\d+)+\\z~', $phone) !== 1) {
			throw new InvalidArgumentException(sprintf('Phone %s is not valid.', $phone));
		}
	}

}
