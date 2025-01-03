<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Crypto;

use function base64_decode;
use function base64_encode;
use function file_get_contents;
use const OPENSSL_ALGO_SHA256;

class CryptoService
{

	public const HASH_METHOD = OPENSSL_ALGO_SHA256;

	private ?string $privateKeyPassword = null;

	public function __construct(
		private string $privateKeyFile,
		private string $bankPublicKeyFile,
		string $privateKeyPassword = '',
	)
	{
		$this->privateKeyPassword = $privateKeyPassword;
	}

	/**
	 * @param mixed[] $data
	 *
	 * @throws PrivateKeyFileException
	 * @throws SigningFailedException
	 */
	public function signData(array $data, SignatureDataFormatter $signatureDataFormatter): string
	{
		$message = $signatureDataFormatter->formatDataForSignature($data);

		/** @var string $privateKey */
		$privateKey = file_get_contents($this->privateKeyFile);
		$privateKeyId = openssl_pkey_get_private($privateKey, (string) $this->privateKeyPassword);
		if ($privateKeyId === false) {
			throw new PrivateKeyFileException($this->privateKeyFile);
		}

		$ok = openssl_sign($message, $signature, $privateKeyId, self::HASH_METHOD);
		if (!$ok) {
			throw new SigningFailedException($data);
		}

		return base64_encode($signature);
	}

	/**
	 * @param mixed[] $data
	 *
	 * @throws PublicKeyFileException
	 * @throws VerificationFailedException
	 */
	public function verifyData(array $data, string $signature, SignatureDataFormatter $signatureDataFormatter): bool
	{
		$message = $signatureDataFormatter->formatDataForSignature($data);

		$publicKey = (string) file_get_contents($this->bankPublicKeyFile);
		$publicKeyId = openssl_pkey_get_public($publicKey);
		if ($publicKeyId === false) {
			throw new PublicKeyFileException($this->bankPublicKeyFile);
		}

		$signature = base64_decode($signature, true);
		if ($signature === false) {
			throw new VerificationFailedException($data, 'Unable to decode signature.');
		}

		$verifyResult = openssl_verify($message, $signature, $publicKeyId, self::HASH_METHOD);
		if ($verifyResult === -1) {
			throw new VerificationFailedException($data, (string) openssl_error_string());
		}

		return $verifyResult === 1;
	}

}
