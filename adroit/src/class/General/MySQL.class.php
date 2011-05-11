<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

// Custom MySQL Client Connection Definitions
define('MYSQL_CLIENT_PERSIST', 1);

// MySQL Result Types
define('MYSQL_ONE_ROW', 1);		// One row at a time, using the next() function.
define('MYSQL_ALL_ROWS', 2);	// All rows, one big object.  Use wisely.

// MySQL Result Filters
define('MYSQL_FILTER_HTML_ENTITIES', 1);

class MySQL
{
	/* CLASS VARIABLES */
	public $link;
	public $query;
	public $result;
	private $previous_db = NULL;

	public $debug = TRUE;
	public $error = FALSE;
	public $error_string = '';
	private $custom_error_msg = '';
	private $called_from;

	/*
	 * CORE FUNCTIONS
	 */
	/**
	 * Turns debugging on (TRUE) and off (FALSE).
	 * @param bool $debug
	 */
	public function set_debug($debug)
	{
		$this->debug = $debug;
	}

	private function check_error($function_name, $param_string = '')
	{
		$called_from = (empty($this->called_from) === FALSE) ? " [Called from {$this->called_from}()]" : '';
		$this->called_from = '';

		$error_msg = (empty($this->custom_error_msg) === FALSE) ? $this->custom_error_msg : mysql_error();
		$this->custom_error_msg = '';

		$this->error_string = ($this->error === TRUE) ?
			"<br />\n<b>Database error</b>: ".get_class($this).'::'.$function_name.'('.$param_string.')'.$called_from.': <b>'.$error_msg."</b><br />\n" :
			'';
		if ($this->debug === TRUE && $this->error === TRUE)
		{
			if (isset($GLOBALS['FirePHP']))
				$GLOBALS['FirePHP']->error(ereg_replace("<[^<>]+>", "", $this->error_string));
			else
				echo $this->error_string;
		}
	}

	private function custom_error($custom_error_msg, $function, $param_string = '')
	{
		$this->error = TRUE;
		$this->custom_error_msg = $custom_error_msg;
		$this->called_from = '';
		$this->check_error($function, $param_string);
	}

	private function inverted_error_flag()
	{
		return ($this->error === TRUE) ? FALSE : TRUE;
	}

	public function get_error()
	{
		return ($this->error === TRUE) ? $this->error_string : FALSE;
	}

	public function connect($host, $user, $pass, $client_flags = FALSE)
	{
		if ($this->error === TRUE)
			return FALSE;

		$persist_flag = ($client_flags & MYSQL_CLIENT_PERSIST) ? TRUE : FALSE;
		$client_flags -= ($persist_flag === TRUE) ? MYSQL_CLIENT_PERSIST : 0;

		if ($persist_flag === TRUE)
		{
			$this->link = @mysql_pconnect($host, $user, $pass, $client_flags);
			$client_flags += 1; // preserve persist flag for error reporting.
		}
		else
			$this->link = @mysql_connect($host, $user, $pass, FALSE, $client_flags);

		$this->error = ($this->link === FALSE) ? TRUE : FALSE;
		$this->check_error(__FUNCTION__, "\"{$host}\", \"{$user}\", \"****\"".(($client_flags != 0)?", {$client_flags}":''));

		return $this->inverted_error_flag();
	}

	public function select_db ($database_name)
	{
		if ($this->error === TRUE)
			return FALSE;

		$this->error = (@mysql_select_db($database_name, $this->link) === FALSE) ? TRUE : FALSE;
		$this->check_error(__FUNCTION__, "\"{$database_name}\"");

		return $this->inverted_error_flag();
	}

	public function toggle_db ($new_database = NULL)
	{
		if (!is_null($new_database))
		{
			if (is_null($this->previous_db))
			{
				if ($this->previous_db = $this->get_database() === FALSE)
					return FALSE;

				if ($this->select_db($new_database) === FALSE)
					return FALSE;

				return TRUE;
			}
		}
		else
		{
			if (!is_null($this->previous_db))
			{
				if ($this->select_db($this->previous_db) === FALSE)
					return FALSE;

				$this->previous_db = NULL;

				return TRUE;
			}
		}

		return FALSE;
	}

	public function escape($mixed)
	{
		if ($this->error === TRUE)
			return FALSE;

		if (is_array($mixed))
		{
			foreach ($mixed as $key => $val)
				$mixed[$key] = $this->escape($val);
		}
		elseif (is_object($mixed))
		{
			foreach ($mixed as $key => $val)
				$mixed->key = $this->escape($val);
		}
		elseif (is_string($mixed) === TRUE)
		{
			$mixed = @mysql_real_escape_string($mixed, $this->link);
			$this->error = ($mixed === FALSE) ? TRUE : FALSE;
			$this->check_error(__FUNCTION__, "\"{$mixed}\"");
		}

		return ($this->error === TRUE) ? FALSE : $mixed;
	}

	public function htmlentities($mixed)
	{
		if ($this->error === TRUE)
			return FALSE;

		if (is_array($mixed))
		{
			foreach ($mixed as $key => $val)
				$mixed[$key] = $this->htmlentities($val);
		}
		elseif (is_object($mixed))
		{
			foreach ($mixed as $key => $val)
				$mixed->key = $this->htmlentities($val);
		}
		elseif (is_string($mixed) === TRUE)
			$mixed = @htmlentities($mixed);

		// For future reference: If error checking needs to be done on htmlentities(),
		// know that htmlentities will *unset* the variable if there's a problem.

		return $mixed;
	}

	public function query($query, $return_type = MYSQL_ALL_ROWS, $filter_type = FALSE)
	{
		if ($this->error === TRUE)
			return FALSE;

		// Store query for later use.
		$this->query = $query;

		// Run query.
		$result = @mysql_query($query, $this->link);
		$this->error = ($result === FALSE) ? TRUE : FALSE;
		$this->check_error(__FUNCTION__, "\"{$query}\", {$return_type}");
		if ($this->error === TRUE)
			return FALSE;

		$this->result = ($return_type === MYSQL_ALL_ROWS) ?
			new MySQL_Result_AllRows($this->link, $result) :
			new MySQL_Result_OneRow($this->link, $result);

		if ($filter_type & MYSQL_FILTER_HTML_ENTITIES)
		{
			if ($return_type === MYSQL_ALL_ROWS)
				$this->result->rows = $this->htmlentities($this->result->rows);
			if ($return_type === MYSQL_ONE_ROW)
				$this->result->row = $this->htmlentities($this->result->row);
		}

		return $this->result;
	}

	/*
	 * EXTRA FUNCTIONS
	 */
	public function get_database()
	{
		$this->called_from = __FUNCTION__;

		$this->query("SELECT DATABASE() AS `Database`");

		if ( $this->result === FALSE || ($this->result !== FALSE && (
					isset($this->result->rows[0]) === FALSE ||
					is_object($this->result->rows[0]) === FALSE ||
					isset($this->result->rows[0]->Database) === FALSE)) )
			return FALSE;

		return $this->result->rows[0]->Database;
	}

	public function get_databases()
	{
		$this->called_from = __FUNCTION__;

		return $this->query("SHOW DATABASES");
	}

	public function db_exists($database_name)
	{
		$this->called_from = __FUNCTION__;

		if ($this->get_databases() === FALSE || ($this->result !== FALSE && !(count($this->result->rows) > 1)))
			return FALSE;

		foreach ($this->result->rows as $row)
		{
			if (!(count($row) >= 1))
				continue;

			foreach ($row as $database)
				if ($database == $database_name)
					return TRUE;
		}

		return FALSE;
	}

	public function get_tables()
	{
		$this->called_from = __FUNCTION__;

		return $this->query("SHOW TABLES");
	}

	public function table_exists($table_name)
	{
		$this->called_from = __FUNCTION__;

		if ($this->get_tables() === FALSE || ($this->result !== FALSE && !(count($this->result->rows) > 1)))
			return FALSE;

		foreach ($this->result->rows as $row)
		{
			if (!(count($row) >= 1))
				continue;

			foreach ($row as $table)
				if ($table == $table_name)
					return TRUE;
		}

		return FALSE;
	}

	// Must be root to to use the following query, most likely.
	// Is there a better way?
	public function get_primary_key($table)
	{
		$original_db = $this->get_database();

		$this->called_from = __FUNCTION__;
		//if ($this->select_db('information_schema') === FALSE)
		//	return FALSE;

		//$this->called_from = __FUNCTION__;
		//$this->query("SELECT `COLUMN_NAME` AS `PrimaryKey` FROM `KEY_COLUMN_USAGE` WHERE `TABLE_NAME` = '{$table}' AND `CONSTRAINT_NAME` = 'PRIMARY'");
		$this->query("SELECT `k`.`column_name` FROM `information_schema`.`table_constraints` `t` JOIN `information_schema`.`key_column_usage` `k` USING(`constraint_name`, `table_schema`, `table_name`) WHERE `t`.`constraint_type` = 'PRIMARY KEY' AND `t`.`table_schema` = '{$original_db}' AND `t`.`table_name` = '{$table}';");
		//die(print_r($this->result));

		//if ($this->result === FALSE)
		//	return FALSE;

		//$this->called_from = __FUNCTION__;
		//if ($this->select_db($original_db) === FALSE)
		//	return FALSE;

		if ($this->result === FALSE || ($this->result !== FALSE && (
					isset($this->result->rows[0]) === FALSE ||
					is_object($this->result->rows[0]) === FALSE ||
					isset($this->result->rows[0]->column_name) === FALSE) ) )
			return FALSE;

		//return $this->result->rows[$last_row]->PrimaryKey;
		return $this->result->rows[0]->column_name;
	}

	public function get_column_names($table)
	{
		$this->called_from = __FUNCTION__;

		// Initialize variables.
		$field_names = array();

		$this->query("SHOW COLUMNS FROM `{$table}`");

		if ($this->result === FALSE || ($this->result !== FALSE &&
			!(count($this->result->rows) >= 1)))
			return FALSE;

		foreach ($this->result->rows as $row)
			$field_names[] = $row->Field;

		return $field_names;
	}

	public function get_field($table, $field, $where)
	{
		$this->called_from = __FUNCTION__;

		$this->query("SELECT `{$field}` FROM `{$table}` WHERE {$where}");

		// Variable checking should be done like this in all functions here.
		if ( $this->result === FALSE || ($this->result !== FALSE && (
					isset($this->result->rows[0]) === FALSE ||
					is_object($this->result->rows[0]) === FALSE ||
					isset($this->result->rows[0]->$field) === FALSE)) )
			return FALSE;

		return $this->result->rows[0]->$field;
	}

	public function get_field_len($table, $field)
	{
		$this->called_from = __FUNCTION__;

		$this->query("SELECT `{$field}` FROM `{$table}` LIMIT 1");

		if ($this->result === FALSE || ($this->result !== FALSE &&
				isset($this->result->field_len->$field) === FALSE))
			return FALSE;

		return $this->result->field_len->$field;
	}

	public function transaction_start()
	{
		$this->called_from = __FUNCTION__;
		return $this->query("START TRANSACTION");
	}

	public function transaction_rollback()
	{
		$this->called_from = __FUNCTION__;
		return $this->query("ROLLBACK");
	}

	public function transaction_commit()
	{
		$this->called_from = __FUNCTION__;
		return $this->query("COMMIT");
	}

	public function insert($data = array(array()))
	{
		if (is_array($data) === FALSE || count($data) === 0)
			return FALSE;

		$resultObject = new MySQL_Result_MultiTable_Generic;

		$this->transaction_start();
		foreach ($data as $table => $fields)
		{
			$value_str = (count($fields) >= 2) ? 'VALUES' : 'VALUE';
			$fields_str = $values_str = '';
			foreach ($fields as $field => $value)
			{
				$fields_str .= ", `{$field}`";
				$values_str .= (is_int($value) || is_float($value) || is_null($value)) ? ", {$value}" : (', \''. $this->escape($value) .'\'');
			}
			$fields_str = substr($fields_str, 2);
			$values_str = substr($values_str, 2);

			$this->called_from = __FUNCTION__;
			$this->query("INSERT INTO `{$table}` ({$fields_str}) {$value_str} ({$values_str})");
			if ($this->error)
			{
				$this->transaction_rollback();
				return FALSE;
			}

			$resultObject->set_insert_id($table, $this->link);
			$resultObject->set_affected_rows($table, $this->link);
		}
		$this->transaction_commit();

		return $resultObject;
	}

	public function update($data = array(array()))
	{
		if (is_array($data) === FALSE || count($data) === 0)
			return FALSE;

		$resultObject = new MySQL_Result_MultiTable_Generic;

		$this->transaction_start();
		foreach ($data as $table => $fields)
		{
			$fields_values_str = $where = '';
			foreach ($fields as $field => $value)
			{
				if (stripos($field, "WHERE") === FALSE)
					$fields_values_str .= ", `{$field}` = " . ((is_int($value) || is_float($value) || is_null($value)) ? $value : ('\''. $this->escape($value) .'\''));
				else
					$where = " WHERE {$value}";
			}
			if (empty($where))
			{
				$this->custom_error('WHERE clause: Not specified (required; use WHERE in place of field)', __FUNCTION__);
				$this->transaction_rollback();
				return FALSE;
			}
			$fields_values_str = substr($fields_values_str, 2);
			$this->query("UPDATE `{$table}` SET {$fields_values_str}{$where}");
			if ($this->error)
			{
				$this->transaction_rollback();
				return FALSE;
			}

			$resultObject->set_affected_rows($table, $this->link);
		}
		$this->transaction_commit();

		return $resultObject;
	}

	public function delete($data = array(array()))
	{
		if (is_array($data) === FALSE || count($data) === 0)
			return FALSE;

		$resultObject = new MySQL_Result_MultiTable_Generic;

		$this->transaction_start();
		foreach ($data as $table => $fields)
		{
			$where = '';
			foreach ($fields as $field => $value)
			{
				if (stripos($field, "WHERE") !== FALSE)
					$where = " WHERE {$value}";
				else
				{
					$this->custom_error('Extra fields: The WHERE field should be the only field specified. '
						.'Check that you aren\'t (re-)using an array that was intended for INSERT or UPDATE operations.',
						__FUNCTION__);
					$this->transaction_rollback();
					return FALSE;
				}
			}
			if (empty($where))
			{
				$this->custom_error('WHERE clause: Not specified (required; use WHERE in place of field)', __FUNCTION__);
				$this->transaction_rollback();
				return FALSE;
			}
			$this->query("DELETE FROM `{$table}`{$where}");
			if ($this->error)
			{
				$this->transaction_rollback();
				return FALSE;
			}

			$resultObject->set_affected_rows($table, $this->link);
		}
		$this->transaction_commit();

		return $resultObject;
	}
}

class MySQL_Result_Base
{
	public $insert_id;
	public $num_rows;
	public $affected_rows;
	public $field_len;
	protected $current_row = 0;

	public function __construct(&$link, &$result)
	{
		$insert_id = @mysql_fetch_row(@mysql_query("SELECT LAST_INSERT_ID()", $link));
		$this->insert_id = ((isset_int($insert_id[0])) ? $insert_id[0] : 0);
		$this->num_rows = @mysql_num_rows($result);
		$this->affected_rows = @mysql_affected_rows($link);
		$this->field_len = new stdClass;
	}
}

class MySQL_Result_OneRow extends MySQL_Result_Base
{
	public $row;
	private $result;

	public function __construct(&$link, &$result)
	{
		parent::__construct($link, $result);
		$this->result = &$result;
		$this->next();
	}

	public function next()
	{
		// Get row.
		$row = @mysql_fetch_assoc($this->result);

		if ($row === FALSE)
		{
			$this->row = FALSE;
			return;
		}

		// Store field lengths.
		if ($this->current_row == 0)
		{
			$col = 0;
			foreach ($row as $field_name => $field_value)
			{
				$this->field_len->$field_name = @mysql_field_len($this->result, $col);
				$col++;
			}
		}
		$this->current_row++;

		// Make row OO.
		return ($this->row = (object)$row);
	}
}

class MySQL_Result_AllRows extends MySQL_Result_Base
{
	public $rows = array();

	public function __construct(&$link, &$result)
	{
		parent::__construct($link, $result);
		while ($row = @mysql_fetch_assoc($result))
		{
			$col = 0;
			foreach ($row as $field_name => $field_value)
			{
				if ($this->current_row == 0)
					$this->field_len->$field_name = @mysql_field_len($result, $col);

				$this->rows[$this->current_row] = (object)$row;
				$col++;
			}
			$this->current_row++;
		}
	}
}

class MySQL_Result_MultiTable_Generic
{
	public $insert_id = array();
	public $affected_rows = array();

	public function set_insert_id($table, &$link)
	{
		$insert_id = @mysql_fetch_row(@mysql_query("SELECT LAST_INSERT_ID()", $link));
		$this->insert_id[$table] = ((isset_int($insert_id[0])) ? $insert_id[0] : 0);
	}

	public function get_insert_id($table)
	{
		return $this->insert_id[$table];
	}

	public function set_affected_rows($table, &$link)
	{
		$this->affected_rows[$table] = @mysql_affected_rows($link);
	}

	public function get_affected_rows($table)
	{
		return $this->affected_rows[$table];
	}
}

class MySQL_Container
{
	public function add($connection_name = 'default', $host, $user, $pass, $client_flags = FALSE)
	{
		$this->$connection_name = new MySQL;
		$this->$connection_name->connect($host, $user, $pass, $client_flags);
	}
}
