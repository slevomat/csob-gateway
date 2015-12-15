<?php

namespace SlevomatCsobGateway\Api\Driver;

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
	return false;
}

function curl_error($ch)
{
	return 'foo error';
}

function curl_errno($ch)
{
	return 11;
}

function curl_getinfo($ch, $opt = null)
{
	return 'foo getinfo';
}

function curl_close($ch)
{

}
