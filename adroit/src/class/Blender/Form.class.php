<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class Form extends XHTML_Element
{
	public $template;

	public function __construct($name, $action, $method)
	{
		parent::__construct('form');
		parent::set_name_and_id($name);
		parent::set('action', $action);
		parent::set('method', $method);
		$this->template = new Template($name);
	}

	public function __toString()
	{
		parent::setContent($this->template);
		return parent::__toString();
	}

	public function render()
	{
		echo $this->__toString();
	}
}
