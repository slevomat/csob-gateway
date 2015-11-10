# CSOB gateway

## Installation

Install and build project:

```
> composer require slevomat/csob-gateway
```


## Usage

initialization

```php
$apiClient = new ApiClient(
	new CurlDriver(),
	new CryptoService(
		$privateKeyFile,
        $publicKeyFile,
        $bankPublicKeyFile
	)
);

$requestFactory = new RequestFactory('012345');

$paymentResponse = $requestFactory->createInitPayment(...)->send($apiClient);
$payId = $paymentResponse->getPayId();

$processPaymentResponse = $requestFactory->createProcessPayment($payId);
echo $processPaymentResponse->getGatewayLocationUrl();

$paymentResponse = $requestFactory->createReceivePaymentRequest()->send($apiClient, $_POST);

```


## Own `ApiClientDriver`


