<?php

namespace SlevomatCsobGateway\Api;

use Closure;
use Psr\Log\LoggerInterface;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;

interface ApiClientInterface
{

    public function setLogger(?LoggerInterface $logger): void;

    public function get(
        string $url,
        array $data,
        SignatureDataFormatter $requestSignatureDataFormatter,
        SignatureDataFormatter $responseSignatureDataFormatter,
        ?Closure $responseValidityCallback = null,
        array $extensions = [],
    ): Response;

    public function post(
        string $url,
        array $data,
        SignatureDataFormatter $requestSignatureDataFormatter,
        SignatureDataFormatter $responseSignatureDataFormatter,
        array $extensions = [],
    ): Response;

    public function put(
        string $url,
        array $data,
        SignatureDataFormatter $requestSignatureDataFormatter,
        SignatureDataFormatter $responseSignatureDataFormatter,
        array $extensions = [],
    ): Response;

    public function createResponseByData(array $data, SignatureDataFormatter $responseSignatureDataFormatter): Response;

}