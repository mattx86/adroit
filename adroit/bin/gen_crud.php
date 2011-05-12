#!/usr/bin/php
<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

define('ADROIT_PATH', dirname(__FILE__).'/../');
include(ADROIT_PATH.'/app/cli.inc.php');

if ($argc != 3)
{
	die("usage: {$argv[0]} <database connection> <table>\n\n");
}
else
{
	$use_db = $argv[1];
	$table = $argv[2];
}

if (!DB_ENABLED)
	die("Error: Database disabled (see DB_ENABLED in app/config/db.conf.php)\n\n");

if (!isset($db->$use_db))
	die("Error: Database connection not set ($use_db) (see app/config/db.conf.php)\n\n");

/*
Pass in table
Find PK to use for get, update and delete
Remove trailing 's' in table name, for use in functions and class name
Generate create, get, update, and delete functions

TABLE: user

create_user($data_array)
get_user($where_clause)
update_user($data_array, $where_clause)
delete_user($where_clause);
*/

$pk = $db->$use_db->get_primary_key($table);
$columns = $db->$use_db->get_column_names($table);
if ($pk !== FALSE && $columns !== FALSE)
{
	$pk_array_key = array_search($pk, $columns);
	if ($pk_array_key !== FALSE)
	{
		unset($columns[$pk_array_key]);
		//echo "PK: ".$pk."\n\n";
	}
}

print_r($columns);
