<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\AdditionalData;

use InvalidArgumentException;
use SlevomatCsobGateway\Encodable;
use SlevomatCsobGateway\EncodeHelper;
use SlevomatCsobGateway\Validator;
use function array_filter;

class FingerprintBrowser implements Encodable
{

	public const HEADER_LENGTH_MAX = 2048;
	public const LANGUAGE_LENGTH_MAX = 8;

	public function __construct(
		private string $userAgent,
		private string $acceptHeader,
		private string $language,
		private bool $javascriptEnabled,
		private ?FingerprintBrowserColorDepth $colorDepth,
		private ?int $screenHeight,
		private ?int $screenWidth,
		private ?int $timezone,
		private ?bool $javaEnabled,
		private ?FingerprintBrowserChallengeWindowSize $challengeWindowSize,
	)
	{
		Validator::checkWhitespacesAndLength($this->userAgent, self::HEADER_LENGTH_MAX);
		Validator::checkWhitespacesAndLength($this->acceptHeader, self::HEADER_LENGTH_MAX);
		Validator::checkWhitespacesAndLength($this->language, self::LANGUAGE_LENGTH_MAX);

		if ($this->javascriptEnabled) {
			if ($this->colorDepth === null) {
				throw new InvalidArgumentException('If javascript is enabled `$colorDepth` is required');
			}
			if ($this->screenHeight === null) {
				throw new InvalidArgumentException('If javascript is enabled `$screenHeight` is required');
			}
			if ($this->screenWidth === null) {
				throw new InvalidArgumentException('If javascript is enabled `$screenWidth` is required');
			}
			if ($this->timezone === null) {
				throw new InvalidArgumentException('If javascript is enabled `$timezone` is required');
			}
			if ($this->javaEnabled === null) {
				throw new InvalidArgumentException('If javascript is enabled `$javaEnabled` is required');
			}
		}

		if ($this->screenHeight !== null) {
			Validator::checkNumberPositive($this->screenHeight);
		}
		if ($this->screenWidth !== null) {
			Validator::checkNumberPositive($this->screenWidth);
		}
	}

	/**
	 * @return mixed[]
	 */
	public function encode(): array
	{
		return array_filter([
			'userAgent' => $this->userAgent,
			'acceptHeader' => $this->acceptHeader,
			'language' => $this->language,
			'javascriptEnabled' => $this->javascriptEnabled,
			'colorDepth' => $this->colorDepth?->value,
			'screenHeight' => $this->screenHeight,
			'screenWidth' => $this->screenWidth,
			'timezone' => $this->timezone,
			'javaEnabled' => $this->javaEnabled,
			'challengeWindowSize' => $this->challengeWindowSize?->value,
		], EncodeHelper::filterValueCallback());
	}

	/**
	 * @return mixed[]
	 */
	public static function encodeForSignature(): array
	{
		return [
			'userAgent' => null,
			'acceptHeader' => null,
			'language' => null,
			'javascriptEnabled' => null,
			'colorDepth' => null,
			'screenHeight' => null,
			'screenWidth' => null,
			'timezone' => null,
			'javaEnabled' => null,
			'challengeWindowSize' => null,
		];
	}

}
