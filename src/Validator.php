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
			throw new \InvalidArgumentException(sprintf(
				sprintf('Name length must be less than %d symbols.', self::CART_ITEM_NAME_LENGTH_MAX)
			));
		}
	}

	/**
	 * @param string $description
	 */
	public static function checkCartItemDescription($description)
	{
		self::checkWhitespaces($description);

		if (strlen(utf8_decode($description)) > self::CART_ITEM_DESCRIPTION_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf(
				sprintf('Description length must be less than %d symbols.', self::CART_ITEM_DESCRIPTION_LENGTH_MAX)
			));
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

		if (strlen(utf8_decode($orderId)) > self::ORDER_ID_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf(
				sprintf('OrderId length must be less than %d symbols.', self::ORDER_ID_LENGTH_MAX)
			));
		}

		if (!preg_match('#^[0-9]+$#', $orderId)) {
			throw new \InvalidArgumentException(sprintf(
				'OrderId must be numeric value. %s given.',
				$orderId
			));
		}
	}

	/**
	 * @param string $returnUrl
	 */
	public static function checkReturnUrl($returnUrl)
	{
		self::checkWhitespaces($returnUrl);

		if (strlen(utf8_decode($returnUrl)) > self::RETURN_URL_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf(
				sprintf('ReturnUrl length must be less than %d symbols.', self::RETURN_URL_LENGTH_MAX)
			));
		}
	}

	/**
	 * @param string $description
	 */
	public static function checkDescription($description)
	{
		self::checkWhitespaces($description);

		if (strlen(utf8_decode($description)) > self::DESCRIPTION_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf(
				sprintf('Description length must be less than %d symbols.', self::DESCRIPTION_LENGTH_MAX)
			));
		}
	}

	/**
	 * @param string $merchantData
	 */
	public static function checkMerchantData($merchantData)
	{
		self::checkWhitespaces($merchantData);

		if (strlen(utf8_decode($merchantData)) > self::MERCHANT_DATA_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf(
				sprintf('MerchantData length must be less than %d symbols.', self::MERCHANT_DATA_LENGTH_MAX)
			));
		}
	}

	/**
	 * @param string $customerId
	 */
	public static function checkCustomerId($customerId)
	{
		self::checkWhitespaces($customerId);

		if (strlen(utf8_decode($customerId)) > self::CUSTOMER_ID_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf(
				sprintf('CustomerId length must be less than %d symbols.', self::CUSTOMER_ID_LENGTH_MAX)
			));
		}
	}

	/**
	 * @param string $payId
	 */
	public static function checkPayId($payId)
	{
		self::checkWhitespaces($payId);

		if (strlen(utf8_decode($payId)) > self::PAY_ID_LENGTH_MAX) {
			throw new \InvalidArgumentException(sprintf(
				sprintf('PayId length must be less than %d symbols.', self::PAY_ID_LENGTH_MAX)
			));
		}
	}

	/**
	 * @param string $argument
	 */
	private static function checkWhitespaces($argument)
	{
		$argument = (string) $argument;

		if ($argument !== trim($argument)) {
			throw new \InvalidArgumentException('Argument starts or ends with whitespace.');
		}
	}

}
