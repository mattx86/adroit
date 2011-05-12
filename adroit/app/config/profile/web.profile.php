<?php
// Adroit Web Profile

// Paths and Locations (no trailing slash)
define('APP_DOCROOT_PATH',		ADROIT_PATH.'/../public'); // Filesystem Path
define('APP_HTTP_PATH',			''); // HTTP Path (empty for 'root' HTTP path)

// General Settings
define('APP_BEAUTIFY',			TRUE);	// Helps with debugging browser output.
define('APP_GZCOMPRESS',		TRUE);	// GZip Compression
define('APP_USERS',				TRUE);	// Users
define('APP_START_SESSION',		TRUE);	// Sessions
define('APP_FORCE_HTTPS',		FALSE);	// Force HTTPS

// Default Content Type
define('APP_CONTENT_TYPE',		'text/html');

// Routes
$app_routes = array(
	'/login/'				=> 'ctrl_login',
	'/logout/'				=> array('ctrl_login', 'logout'),
	'/'						=> 'ctrl_home'
);

// Routes - Pre-Login
$app_routes_pre_login = array(
	'/install/'				=> 'ctrl_install'
);