<?php

// Adroit Database Configuration
$db = new MySQL_Container;

// Default Database Connection (use first)
/*
$db->add('default', 'localhost', 'dbuser', 'dbpass');
$db->default->select_db('dbname');
*/

// Additional Database Connections
/*
$db->add('connection_name', 'localhost', 'dbuser', 'dbpass');
$db->connection_name->select_db('dbname');
*/
