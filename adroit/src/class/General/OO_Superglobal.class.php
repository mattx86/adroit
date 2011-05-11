<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class OO_Superglobal
{
	public $global;

	public function __construct($global)
	{
		$this->global = $global;
	}

	public function __get($variable)
	{
		$data = (($this->__isset($variable))?$GLOBALS[$this->global][$variable]:'');
		
		return new OO_Superglobal_Escape($data);
	}

	public function __isset($variable)
	{
		return isset($GLOBALS[$this->global][$variable]);
	}

	public function __unset($variable)
	{
		unset($GLOBALS[$this->global][$variable]);
	}

	public static function init_multiple($variables = array())
	{
		foreach ($variables as $variable)
		{
			$var_name = ucfirst(strtolower(substr($variable, 1)));
			$GLOBALS[$var_name] = new OO_Superglobal($variable);
		}
	}
}

class OO_Superglobal_Escape
{
	public $original;
	
	public function __construct(&$original)
	{
		$this->original = @$original;
	}
	
	public function esc_html()
	{
		return @htmlentities($this->original);		
	}
	
	public function esc_mysql($db_object = NULL)
	{
		if (isset($GLOBALS['db']) && is_object($GLOBALS['db']) && $GLOBALS['db']->link !== FALSE)
			return @$GLOBALS['db']->escape_string($this->original);

		return @mysql_escape_string($this->original);
	}
	
	public function __toString()
	{
		return $this->original;
	}
}
?>