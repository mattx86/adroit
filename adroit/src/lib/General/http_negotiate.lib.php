<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

function __http_negotiate_charset($supported = array())
{
	if (isset($_SERVER['HTTP_ACCEPT_CHARSET']) === FALSE)
		return $supported[0];

	$accept_charset = explode(';', $_SERVER['HTTP_ACCEPT_CHARSET']);
	$client = explode(',', $accept_charset[0]);

	foreach ($supported as $sup_charset)
		foreach ($client as $cli_charset)
			if (strtolower($sup_charset) == strtolower($cli_charset))
				return $sup_charset;

	return $supported[0];
}

function __http_negotiate_language($supported = array())
{
	if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) === FALSE)
		return $supported[0];

	$accept_language = explode(';', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
	$client = explode(',', $accept_language[0]);

	foreach ($supported as $sup_language)
		foreach ($client as $cli_language)
			if (strtolower($sup_language) == strtolower($cli_language))
				return $sup_language;

	return $supported[0];
}
