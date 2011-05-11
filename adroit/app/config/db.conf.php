<?php

// Adroit Database Configuration
define('DB_ENABLED',			TRUE);

if (DB_ENABLED)
{
	$db = new MySQL_Container;
	
	$db->add('test', '127.0.0.1', 'dbuser', 'dbpass');
	$db->test->select_db('test');
}
