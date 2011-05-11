<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

// Include and configure the Loader class
include('src/Loader.class.php');
$Loader = new Loader(ADROIT_PATH);
function __autoload($class_name)
{
	global $Loader;
	
	$Loader->load_class($class_name);
}


// Load some libraries
$Loader->load_lib('isset');

// Include some configuration files
include('app/config/app.conf.php');
include('app/config/db.conf.php');
