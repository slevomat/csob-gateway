# CSOB gateway

[![Build status](https://github.com/slevomat/csob-gateway/workflows/Build/badge.svg?branch=master)](https://github.com/slevomat/csob-gateway/actions?query=workflow%3ABuild+branch%3Amaster)
[![Code coverage](https://codecov.io/gh/slevomat/csob-gateway/branch/master/graph/badge.svg)](https://codecov.io/gh/slevomat/csob-gateway)
[![Latest Stable Version](https://img.shields.io/packagist/v/slevomat/csob-gateway.svg)](https://packagist.org/packages/slevomat/csob-gateway)
[![Composer Downloads](https://img.shields.io/packagist/dt/slevomat/csob-gateway.svg)](https://packagist.org/packages/slevomat/csob-gateway)

This repository provides a client library for ČSOB Payment Gateway.

- [CSOB payment gateway wiki](https://github.com/csob/paymentgateway/wiki)
- [CSOB eAPI 1.9](https://github.com/csob/platebnibrana/wiki/Vol%C3%A1n%C3%AD-rozhran%C3%AD-eAPI)

Library supports **all endpoints of eAPI 1.9** except NEJsplátku (loan@shop). Pull requests are welcome.

Older available versions (not actively maintained):
- Version 5.* supports PHP 7.2 and eAPI 1.8
- Version 4.* supports PHP 7.2 and eAPI 1.7
- Version 3.* supports PHP 7 and eAPI 1.6.
- Version 2.* supports PHP 7 and eAPI 1.5.
- Version 1.* supports PHP 5.6 and eAPI 1.5.

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
	),
	'https://api.platebnibrana.csob.cz/api/v1.8'
);

$requestFactory = new RequestFactory('012345');

// cart has to have at least 1 but most of 2 items
$cart = new Cart(Currency::EUR);
$cart->addItem('Nákup', 1, 1.9 * 100);

$customer = new Customer(
    'Jan Novák',
    'jan.novak@example.com',
    mobilePhone: '+420.800300300',
    customerAccount: new CustomerAccount(
        new DateTimeImmutable('2022-01-12T12:10:37+01:00'),
        new DateTimeImmutable('2022-01-15T15:10:12+01:00'),
    ),
    customerLogin: new CustomerLogin(
        CustomerLoginAuth::ACCOUNT,
        new DateTimeImmutable('2022-01-25T13:10:03+01:00'),
    ),
);

$order = new Order(
    OrderType::PURCHASE,
    OrderAvailability::NOW,
    null,
    OrderDelivery::SHIPPING,
    OrderDeliveryMode::SAME_DAY,
    addressMatch: true,
    billing: new OrderAddress(
        'Karlova 1',
        null,
        null,
        'Praha',
        '11000',
        null,
        Country::CZE,
    ),
);

$paymentResponse = $requestFactory->createInitPayment(
	123,
	PayOperation::PAYMENT,
	PayMethod::CARD,
	true,
	$returnUrl,
	HttpMethod::POST,
	$cart,
    $customer,
    $order,
    'some-base64-encoded-merchant-data',
    '123',
    Language::CZ,
    1800,
    1,
    2,
)->send($apiClient);
$payId = $paymentResponse->getPayId();

$processPaymentResponse = $requestFactory->createProcessPayment($payId)->send($apiClient);

// redirect to gateway
header('Location: ' . $processPaymentResponse->getGatewayLocationUrl());
```
After customer returns from gateway, he is redirected to `$returnUrl` where you have to process the payment.
```php
try {
    $receivePaymentResponse = $requestFactory->createReceivePaymentRequest()->send($apiClient, $_POST /* $_GET */);
    if ($receivePaymentResponse->getPaymentStatus() === PaymentStatus::S7_AWAITING_SETTLEMENT) {
        // payment was successful!
    }
} catch (VerificationFailedException | InvalidSignatureException $e) {
    // request was not send from csob api
}
```
Please refer to the CSOB documentation and learn what states you should to check, they are all available as PaymentStatus::S* constants.

## Custom `ApiClientDriver`

API calls are made through `ApiClientDriver` interface. Library contains two default implementations of driver - CurlDriver and GuzzleDriver. You can also
create your own driver by implementing the `ApiClientDriver` interface, and passing it to `ApiClient` constructor.

`CurlDriver` communicates via `curl` PHP extension, `GuzzleDriver` uses [guzzlehttp/guzzle](https://packagist.org/packages/guzzlehttp/guzzle) library. If you want to use
GuzzleDriver you need to require `guzzlehttp/guzzle` package in your composer.json.
