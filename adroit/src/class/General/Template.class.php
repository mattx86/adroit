<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class Template
{
	private $object;
	private $function;
	private $vars;
	private $output;
	
	public function __construct($class_name, $class_function = 'main')
	{
		// Instantiate the template class
		$this->object = new $class_name;
		
		// Store the class function used for processing the template.
		$this->function = $class_function;
		
		// Create a reference from within the template object, to the
		// local template variables.
		//$this->object->vars =& $this->vars;
		
		// Initialize the template output variable.
		$this->output = NULL;
		
		// OO-Constants
		if (isset($GLOBALS['Constants']))
			$this->object->Constants =& $GLOBALS['Constants'];
	}
	
	public function set($var, $val)
	{
		//$this->vars[$var] = $val;
		$this->object->$var = $val;
	}
	
	public function process($class_function = NULL)
	{
		if (!is_null($class_function))
			$this->function = $class_function;
		
		ob_start();
		$this->object->{$this->function}();
		$this->output = ob_get_contents();
		ob_end_clean();
	}
	
	public function __toString()
	{
		if (is_null($this->output))
			$this->process();
		
		return $this->output;
	}
}