<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api;

use SlevomatCsobGateway\Call\ProcessPaymentRequest;
use function sprintf;

class InvalidPaymentException extends RequestException
{

	/** @var ProcessPaymentRequest */
	private $request;

	public function __construct(ProcessPaymentRequest $request, Response $response, string $payId)
	{
		parent::__construct(sprintf('PayId %s is invalid or expired.', $payId), $response);
		$this->request = $request;
	}

	public function getRequest(): ProcessPaymentRequest
	{
		return $this->request;
	}

}
