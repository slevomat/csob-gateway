<?php

namespace SlevomatCsobGateway\Api;

use SlevomatCsobGateway\Call\ProcessPaymentRequest;

class InvalidPaymentException extends RequestException
{

	/** @var ProcessPaymentRequest */
	private $request;

	/**
	 * @param ProcessPaymentRequest $request
	 * @param Response $response
	 * @param string $payId
	 */
	public function __construct(ProcessPaymentRequest $request, Response $response, $payId)
	{
		parent::__construct(sprintf('PayId %s is invalid or expired.', $payId), $response);
		$this->request = $request;
	}

	/**
	 * @return ProcessPaymentRequest
	 */
	public function getRequest()
	{
		return $this->request;
	}

}
