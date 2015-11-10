<?php

namespace SlevomatCsobGateway\Crypto;

class CryptoService
{

	const HASH_METHOD = OPENSSL_ALGO_SHA1;

	/**
	 * @var string
	 */
	private $privateKeyFile;

	/**
	 * @var string
	 */
	private $publicKeyFile;

	/**
	 * @var string
	 */
	private $bankPublicKeyFile;

	/**
	 * @var string|null
	 */
	private $privateKeyPassword;

	/**
	 * @var mixed[]
	 */
	private $keysPriority = [
		'merchantId' => null,
		'customerId' => null,
		'orderNo' => null,
		'payId' => null,
		'dttm' => null,
		'resultCode' => null,
		'resultMessage' => null,
		'paymentStatus' => null,
		'authCode' => null,
		'payOperation' => null,
		'payMethod' => null,
		'totalAmount' => null,
		'currency' => null,
		'closePayment' => null,
		'returnUrl' => null,
		'returnMethod' => null,
		'cart' => [
			'name' => null,
			'quantity' => null,
			'amount' => null,
			'description' => null,
		],
		'description' => null,
		'merchantData' => null,
		'language' => null,
	];

	/**
	 * @param string $privateKeyFile
	 * @param string $publicKeyFile
	 * @param string $bankPublicKeyFile
	 * @param string|null $privateKeyPassword
	 */
	public function __construct(
		$privateKeyFile,
		$publicKeyFile,
		$bankPublicKeyFile,
		$privateKeyPassword = null
	)
	{
		$this->privateKeyFile = (string) $privateKeyFile;
		$this->publicKeyFile = (string) $publicKeyFile;
		$this->bankPublicKeyFile = (string) $bankPublicKeyFile;
		$this->privateKeyPassword = $privateKeyPassword !== null ? (string) $privateKeyPassword : null;
	}

	/**
	 * @param mixed[] $data
	 * @param SignatureDataFormatter $signatureDataFormatter
	 * @return string
	 * @throws PrivateKeyFileException
	 * @throws SigningFailedException
	 */
	public function signData(array $data, SignatureDataFormatter $signatureDataFormatter)
	{
		$message = $signatureDataFormatter->formatDataForSignature($data);

		$privateKey = file_get_contents($this->privateKeyFile);
		$privateKeyId = openssl_pkey_get_private($privateKey, $this->privateKeyPassword);
		if ($privateKeyId === false) {
			throw new PrivateKeyFileException($this->privateKeyFile);
		}

		$ok = openssl_sign($message, $signature, $privateKeyId, self::HASH_METHOD);
		if (!$ok) {
			throw new SigningFailedException($data);
		}

		$signature = base64_encode($signature);
		openssl_free_key($privateKeyId);

		return $signature;
	}

	/**
	 * @param mixed[] $data
	 * @param string $signature
	 * @param SignatureDataFormatter $signatureDataFormatter
	 * @return bool
	 * @throws PublicKeyFileException
	 * @throws VerificationFailedException
	 */
	public function verifyData(array $data, $signature, SignatureDataFormatter $signatureDataFormatter)
	{
		$message = $signatureDataFormatter->formatDataForSignature($data);

		$publicKey = file_get_contents($this->bankPublicKeyFile);
		$publicKeyId = openssl_pkey_get_public($publicKey);
		if ($publicKeyId === false) {
			throw new PublicKeyFileException($this->bankPublicKeyFile);
		}

		$signature = base64_decode($signature);

		$verifyResult = openssl_verify($message, $signature, $publicKeyId, self::HASH_METHOD);
		openssl_free_key($publicKeyId);
		if ($verifyResult === -1) {
			throw new VerificationFailedException($data, openssl_error_string());
		}

		return $verifyResult === 1;
	}

}
