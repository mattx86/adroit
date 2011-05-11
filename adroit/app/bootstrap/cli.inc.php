<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

chdir(ADROIT_PATH);
include('app/bootstrap/__common.inc.php');
include('app/config/profile/cli.profile.php');

// Language and Charset
$app_language = $app_languages[0];
$app_language_short = substr($app_language, 0, 2);
$app_charset = $app_charsets[0];
iconv_set_encoding('output_encoding', strtoupper($app_charset));
iconv_set_encoding('input_encoding', strtoupper($app_charset));
iconv_set_encoding('internal_encoding', strtoupper($app_charset));

// Set Locale
setlocale(LC_ALL, str_replace('-', '_', $app_language).'.'.str_replace('-', '', $app_charset));

// Set Timezone
date_default_timezone_set($app_timezone);

// OO-Constants
$Constants = new Constants;