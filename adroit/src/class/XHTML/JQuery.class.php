<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class JQuery_Document_Ready extends Script
{
	public $js_functions = array();
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function add($js_function_string)
	{
		$this->js_functions[] = @$js_function_string;
	}
	
	public function __toString()
	{
		$this->setContent("\$(document).ready(function() {\n");
		foreach ($this->js_functions as $js_function)
			$this->appendContent($js_function."\n");
		$this->appendContent("}); // END: JQuery Document Ready\n");
		
		return parent::__toString();
	}
}
?>