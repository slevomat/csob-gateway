<?php declare(strict_types = 1);

namespace SlevomatCsobGateway;

class Validator
{

	const CART_ITEM_NAME_LENGTH_MAX = 20;
	const CART_ITEM_DESCRIPTION_LENGTH_MAX = 40;

	const ORDER_ID_LENGTH_MAX = 10;
	const RETURN_URL_LENGTH_MAX = 300;
	const DESCRIPTION_LENGTH_MAX = 255;
	const MERCHANT_DATA_LENGTH_MAX = 255;
	const CUSTOMER_ID_LENGTH_MAX = 50;
	const PAY_ID_LENGTH_MAX = 15;

	const TTL_SEC_MIN = 300;
	const TTL_SEC_MAX = 1800;

	public static function checkCartItemName(string $name)
	{
		self::checkWhitespaces($name);

		if (strlen(utf8_decode($name)) > self::CART_ITEM_NAME_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf('Cart item name can have maximum of %d characters.', self::CART_ITEM_NAME_LENGTH_MAX));
		}
	}

	public static function checkCartItemDescription(string $description)
	{
		self::checkWhitespaces($description);

		if (strlen(utf8_decode($description)) > self::CART_ITEM_DESCRIPTION_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf('Cart item description can have maximum of %d characters.', self::CART_ITEM_DESCRIPTION_LENGTH_MAX));
		}
	}

	public static function checkCartItemQuantity(int $quantity)
	{
		if ($quantity < 1) {
			throw new \InvalidArgumentException(sprintf(
				'Quantity must be greater than 0. %d given.',
				$quantity
			));
		}
	}

	public static function checkOrderId(string $orderId)
	{
		self::checkWhitespaces($orderId);

		if (!ctype_digit($orderId)) {
			throw new \InvalidArgumentException(sprintf(
				'OrderId must be numeric value. %s given.',
				$orderId
			));
		}

		if (strlen($orderId) > self::ORDER_ID_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf('OrderId can have maximum of %d characters.', self::ORDER_ID_LENGTH_MAX));
		}

	}

	public static function checkReturnUrl(string $returnUrl)
	{
		self::checkWhitespaces($returnUrl);

		if (strlen(utf8_decode($returnUrl)) > self::RETURN_URL_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf('ReturnUrl can have maximum of %d characters.', self::RETURN_URL_LENGTH_MAX));
		}
	}

	public static function checkDescription(string $description)
	{
		self::checkWhitespaces($description);

		if (strlen(utf8_decode($description)) > self::DESCRIPTION_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf('Description can have maximum of %d characters.', self::DESCRIPTION_LENGTH_MAX));
		}
	}

	public static function checkMerchantData(string $merchantData)
	{
		self::checkWhitespaces($merchantData);

		if (strlen(utf8_decode(base64_encode($merchantData))) > self::MERCHANT_DATA_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf('MerchantData can have maximum of %d characters in encoded state.', self::MERCHANT_DATA_LENGTH_MAX));
		}
	}

	public static function checkCustomerId(string $customerId)
	{
		self::checkWhitespaces($customerId);

		if (strlen(utf8_decode($customerId)) > self::CUSTOMER_ID_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf('CustomerId can have maximum of %d characters.', self::CUSTOMER_ID_LENGTH_MAX));
		}
	}

	public static function checkPayId(string $payId)
	{
		self::checkWhitespaces($payId);

		if (strlen(utf8_decode($payId)) > self::PAY_ID_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf('PayId can have maximum of %d characters.', self::PAY_ID_LENGTH_MAX));
		}
	}

	private static function checkWhitespaces(string $argument)
	{
		$charlist = preg_quote(" \t\n\r\0\x0B\xC2\xA0", '#');
		preg_replace('#^[' . $charlist . ']+|[' . $charlist . ']+\z#u', '', $argument);

		if ($argument !== preg_replace('#^[' . $charlist . ']+|[' . $charlist . ']+\z#u', '', $argument)) {
			throw new \InvalidArgumentException('Argument starts or ends with whitespace.');
		}
	}

	public static function checkTtlSec(int $ttlSec)
	{
		if ($ttlSec < self::TTL_SEC_MIN || $ttlSec > self::TTL_SEC_MAX) {
			throw new \InvalidArgumentException(sprintf('TTL sec is out of range (%d - %d). Current value is %d.', self::TTL_SEC_MIN, self::TTL_SEC_MAX, $ttlSec));
		}
	}

}
