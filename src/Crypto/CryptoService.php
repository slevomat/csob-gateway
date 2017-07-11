<?php declare(strict_types = 1);

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
	private $bankPublicKeyFile;

	/**
	 * @var string|null
	 */
	private $privateKeyPassword;

	public function __construct(
		string $privateKeyFile,
		string $bankPublicKeyFile,
		string $privateKeyPassword = ''
	)
	{
		$this->privateKeyFile = $privateKeyFile;
		$this->bankPublicKeyFile = $bankPublicKeyFile;
		$this->privateKeyPassword = $privateKeyPassword;
	}

	/**
	 * @param mixed[] $data
	 * @param SignatureDataFormatter $signatureDataFormatter
	 * @return string
	 * @throws PrivateKeyFileException
	 * @throws SigningFailedException
	 */
	public function signData(array $data, SignatureDataFormatter $signatureDataFormatter): string
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
	public function verifyData(array $data, string $signature, SignatureDataFormatter $signatureDataFormatter): bool
	{
		$message = $signatureDataFormatter->formatDataForSignature($data);

		$publicKey = file_get_contents($this->bankPublicKeyFile);
		$publicKeyId = openssl_pkey_get_public($publicKey);
		if ($publicKeyId === false) {
			throw new PublicKeyFileException($this->bankPublicKeyFile);
		}

		$signature = base64_decode($signature);
		if ($signature === false) {
			throw new VerificationFailedException($data, 'Unable to decode signature.');
		}

		$verifyResult = openssl_verify($message, $signature, $publicKeyId, self::HASH_METHOD);
		openssl_free_key($publicKeyId);
		if ($verifyResult === -1) {
			throw new VerificationFailedException($data, openssl_error_string());
		}

		return $verifyResult === 1;
	}

}
