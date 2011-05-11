#!/usr/bin/php
<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

// Display usage
if (!isset($argv[1]))
	die("usage: $argv[0] <path to adroit loader config file>\n\n");

$path = $argv[1];
$conf = unserialize(file_get_contents($path));

var_dump($conf);
