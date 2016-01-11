# CSOB gateway

[![Build Status](https://img.shields.io/travis/slevomat/csob-gateway/master.svg?style=flat-square)](https://travis-ci.org/slevomat/csob-gateway)
[![Code Coverage](https://img.shields.io/coveralls/slevomat/csob-gateway.svg?style=flat-square)](https://coveralls.io/r/slevomat/csob-gateway)
[![Latest Stable Version](https://img.shields.io/packagist/v/slevomat/csob-gateway.svg?style=flat-square)](https://packagist.org/packages/slevomat/csob-gateway)
[![Composer Downloads](https://img.shields.io/packagist/dt/slevomat/csob-gateway.svg?style=flat-square)](https://packagist.org/packages/slevomat/csob-gateway)

This repository provides a client library for ČSOB Payment Gateway.

- [CSOB payment gateway wiki](https://github.com/csob/paymentgateway/wiki)
- [CSOB eAPI 1.5](https://github.com/csob/paymentgateway/wiki/eAPI-1.5)

Library supports all endpoints of eAPI 1.5.

## Installation

The best way to install slevomat/csob-gateway is using [Composer](http://getcomposer.org/):

```
> composer require slevomat/csob-gateway
```

## Usage

First you have to initialize the payment in gateway and redirect customer to its interface.

**WARNING**: Please note, that all the prices are in hundredths of currency units. It means that when you wanna init a payment for 1.9 EUR, you should pass here the integer 190.
```php
$apiClient = new ApiClient(
	new CurlDriver(),
	new CryptoService(
		$privateKeyFile,
		$bankPublicKeyFile
	)
);

$requestFactory = new RequestFactory('012345');

// cart has to have at least 1 but most of 2 items
$cart = new Cart(new Currency(Currency::EUR));
$cart->addItem('Nákup', 1, 1.9 * 100);

$paymentResponse = $requestFactory->createInitPayment(
	123,
	new PayOperation(PayOperation::PAYMENT),
	new PayMethod(PayMethod::CARD),
	true,
	$returnUrl,
	new HttpMethod(HttpMethod::POST),
	$cart
)->send($apiClient);
$payId = $paymentResponse->getPayId();

$processPaymentResponse = $requestFactory->createProcessPayment($payId);

// redirect to gateway
header('Location: ' . $processPaymentResponse->getGatewayLocationUrl());
```
After customer returns from gateway, he is redirected to `$returnUrl` where you have to process the payment.
```
$paymentResponse = $requestFactory->createReceivePaymentRequest()->send($apiClient, $_POST);
if ($paymentResponse->getPaymentStatus()->equalsValue(PaymentStatus::S7_AWAITING_SETTLEMENT)) {
	// payment was successful!
}
```
Please refer to the CSOB documentation and learn what states you should to check, they are all available as PaymentStatus::S* constants.

## Custom `ApiClientDriver`

API calls are made through `ApiClientDriver` interface. Library contains two default implementations of driver - CurlDriver and GuzzleDriver. You can also
create your own driver by implementing the `ApiClientDriver` interface, and passing it to `ApiClient` constructor.

`CurlDriver` communicates via `curl` PHP extension, `GuzzleDriver` uses [guzzlehttp/guzzle](https://packagist.org/packages/guzzlehttp/guzzle) library. If you want to use
GuzzleDriver you need to require `guzzlehttp/guzzle` package in your composer.json.
