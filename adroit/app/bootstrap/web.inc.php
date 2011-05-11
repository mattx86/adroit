<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

chdir(ADROIT_PATH);
include('app/bootstrap/__common.inc.php');
include('app/config/profile/web.profile.php');

// Initialize $Server, $Get, $Post, $Files, and $Cookie as OO_Superglobals.
OO_Superglobal::init_multiple(array(
		'_SERVER', '_GET', '_POST', '_FILES', '_COOKIE'
	)
);

// Load the http_negotiate library.
$Loader->load_lib('http_negotiate');

// Language and Charset Negotiation
$app_language = __http_negotiate_language($app_languages);
$app_language_short = substr($app_language, 0, 2);
$app_charset = __http_negotiate_charset($app_charsets);
iconv_set_encoding('output_encoding', strtoupper($app_charset));
iconv_set_encoding('input_encoding', strtoupper($app_charset));
iconv_set_encoding('internal_encoding', strtoupper($app_charset));

// Set Locale
setlocale(LC_ALL, str_replace('-', '_', $app_language).'.'.str_replace('-', '', $app_charset));

// Set Timezone
date_default_timezone_set($app_timezone);

// OO-Constants
$Constants = new Constants;

// Initial Headers
$Headers = new Headers;
$Headers->add('Content-Type: '. APP_CONTENT_TYPE .'; charset='. strtoupper($app_charset));

// GZip Compression
if (APP_GZCOMPRESS)
	ob_start("ob_gzhandler");

// Start Session
// Also functions like an OO_Superglobal ($Session)
$Session = new Session(APP_START_SESSION);

if (APP_USERS)
	$users = new Users($db->test);

// Determine if a redirect is required.
$__redirect = FALSE;
$__redirect_location = 'http';
if (APP_FORCE_HTTPS && !isset($_SERVER['HTTPS'])) {
	$__redirect_location .= 's';
	$__redirect = TRUE;
}
$__redirect_location .= '://'.$_SERVER['HTTP_HOST'];
if (APP_USERS && !isset($Session->user_id) && $_SERVER['REQUEST_URI'] != APP_HTTP_PATH.'/login/' && !in_array(str_replace(APP_HTTP_PATH, '', $_SERVER['REQUEST_URI']), $app_routes_pre_login))
{
	$__redirect_location .= APP_HTTP_PATH.'/login/';
	$__redirect = TRUE;
}
else
	$__redirect_location .= $_SERVER['REQUEST_URI'];

if (APP_USERS && in_array(str_replace(APP_HTTP_PATH, '', $_SERVER['REQUEST_URI']), $app_routes_pre_login))
	$__redirect = FALSE;

if ($__redirect) {
	header("Location: {$__redirect_location}");
	exit;
}

// Route URLs to controllers
URLRouter::Route($app_routes, APP_HTTP_PATH);

// Send Headers
$Headers->send();