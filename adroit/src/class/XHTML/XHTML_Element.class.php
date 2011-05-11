<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class XHTML_Element
{
	protected $name;
	protected $attributes = array ();
	protected $content = NULL;
	
	public function __construct ($name, $content = NULL)
	{
		$this->name = @$name;
		$this->content = @$content;
		
		if ($name == 'html')
			$this->xmlns = 'http://www.w3.org/1999/xhtml';
	}
	
	public function __set ($attribute_name, $attribute_value)
	{
		$this->attributes[$attribute_name] = @$attribute_value;
	}
	
	public function set ($attribute_name, $attribute_value)
	{
		$this->__set ($attribute_name, $attribute_value);
	}
	
	public function set_name_and_id ($name_value = NULL)
	{
		if ($name_value !== NULL)
		{
			$this->__set ('name', @$name_value);
			$this->__set ('id', @$name_value);
		}
	}
	
	public function __get ($attribute_name)
	{
		return @$this->attributes[$attribute_name];
	}
	
	public function get ($attribute_name)
	{
		return $this->__get($attribute_name);
	}
	
	public function setContent ($content)
	{
		$this->content = @$content;
	}
	
	public function appendContent ($content)
	{
		$this->content .= @$content;
	}
	
	public function getContent ()
	{
		return $this->content;
	}
	
	public function __toString()
	{
		$attribute_str = '';
		foreach ($this->attributes as $attribute_name => $attribute_value)
		{
//			$attribute_value_ent = htmlentities ($attribute_value);
			$attribute_str .= " {$attribute_name}=\"{$attribute_value}\"";
		}
		
		$end_tag_str = ($this->content === NULL) ? ' /' : 
			">{$this->content}</{$this->name}";

		return "<{$this->name}{$attribute_str}{$end_tag_str}>";
	}
}

?>