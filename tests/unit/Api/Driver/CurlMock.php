<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Api\Driver;

use const CURLINFO_HEADER_SIZE;
use const CURLINFO_HTTP_CODE;

function curl_init($url = null)
{
	return null;
}

function curl_setopt($ch, $option, $value)
{
	return null;
}

function curl_exec($ch)
{
	return 'abc:def{"text": "foo text"}';
}

function curl_getinfo($ch, $opt = null)
{
	if ($opt === CURLINFO_HEADER_SIZE) {
		return 7;
	}

	if ($opt === CURLINFO_HTTP_CODE) {
		return 200;
	}

	return null;
}

function curl_close($ch)
{
}
