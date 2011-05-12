<?php

// Adroit Database Configuration
define('DB_ENABLED',			FALSE);

if (DB_ENABLED)
{
	$db = new MySQL_Container;
	
	$db->add('connection_name', 'localhost', 'dbuser', 'dbpass');
	$db->connection_name->select_db('dbname');
}
