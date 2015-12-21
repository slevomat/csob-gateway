<?php

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

	/**
	 * @param string $name
	 */
	public static function checkCartItemName($name)
	{
		self::checkWhitespaces($name);

		if (strlen(utf8_decode($name)) > self::CART_ITEM_NAME_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf('Cart item name can have maximum of %d characters.', self::CART_ITEM_NAME_LENGTH_MAX));
		}
	}

	/**
	 * @param string $description
	 */
	public static function checkCartItemDescription($description)
	{
		self::checkWhitespaces($description);

		if (strlen(utf8_decode($description)) > self::CART_ITEM_DESCRIPTION_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf('Cart item description can have maximum of %d characters.', self::CART_ITEM_DESCRIPTION_LENGTH_MAX));
		}
	}

	/**
	 * @param int $quantity
	 */
	public static function checkCartItemQuantity($quantity)
	{
		if ($quantity < 1) {
			throw new \InvalidArgumentException(sprintf(
				'Quantity must be greater than 0. %d given.',
				$quantity
			));
		}
	}

	/**
	 * @param string $orderId
	 */
	public static function checkOrderId($orderId)
	{
		self::checkWhitespaces($orderId);

		if (preg_match('#^[0-9]+$#', $orderId) === 0) {
			throw new \InvalidArgumentException(sprintf(
				'OrderId must be numeric value. %s given.',
				$orderId
			));
		}

		if (strlen((string) $orderId) > self::ORDER_ID_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf('OrderId can have maximum of %d characters.', self::ORDER_ID_LENGTH_MAX));
		}

	}

	/**
	 * @param string $returnUrl
	 */
	public static function checkReturnUrl($returnUrl)
	{
		self::checkWhitespaces($returnUrl);

		if (strlen(utf8_decode($returnUrl)) > self::RETURN_URL_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf('ReturnUrl can have maximum of %d characters.', self::RETURN_URL_LENGTH_MAX));
		}
	}

	/**
	 * @param string $description
	 */
	public static function checkDescription($description)
	{
		self::checkWhitespaces($description);

		if (strlen(utf8_decode($description)) > self::DESCRIPTION_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf('Description can have maximum of %d characters.', self::DESCRIPTION_LENGTH_MAX));
		}
	}

	/**
	 * @param string $merchantData
	 */
	public static function checkMerchantData($merchantData)
	{
		self::checkWhitespaces($merchantData);

		if (strlen(utf8_decode($merchantData)) > self::MERCHANT_DATA_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf('MerchantData can have maximum of %d characters.', self::MERCHANT_DATA_LENGTH_MAX));
		}
	}

	/**
	 * @param string $customerId
	 */
	public static function checkCustomerId($customerId)
	{
		self::checkWhitespaces($customerId);

		if (strlen(utf8_decode($customerId)) > self::CUSTOMER_ID_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf('CustomerId can have maximum of %d characters.', self::CUSTOMER_ID_LENGTH_MAX));
		}
	}

	/**
	 * @param string $payId
	 */
	public static function checkPayId($payId)
	{
		self::checkWhitespaces($payId);

		if (strlen(utf8_decode($payId)) > self::PAY_ID_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf('PayId can have maximum of %d characters.', self::PAY_ID_LENGTH_MAX));
		}
	}

	/**
	 * @param string $argument
	 */
	private static function checkWhitespaces($argument)
	{
		$argument = (string) $argument;
		$charlist = preg_quote(" \t\n\r\0\x0B\xC2\xA0", '#');
		preg_replace('#^[' . $charlist . ']+|[' . $charlist . ']+\z#u', '', $argument);

		if ($argument !== preg_replace('#^[' . $charlist . ']+|[' . $charlist . ']+\z#u', '', $argument)) {
			throw new \InvalidArgumentException('Argument starts or ends with whitespace.');
		}
	}

}
