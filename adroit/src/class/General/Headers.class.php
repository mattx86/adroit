<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class Headers
{
	public $headers = array();

	public function add($str)
	{
		$this->headers[] = $str;
	}
	
	public function find($str)
	{
		foreach ($this->headers as $key => $header)
			if (stripos($header, $str) !== FALSE)
				return $key;
		
		return FALSE;
	}
	
	public function replace($key, $str)
	{
		$this->headers[$key] = $str;
	}
	
	public function findAndReplace($find, $replace)
	{
		$key = $this->find($find);
		$this->replace($key, $replace);
	}

	public function send()
	{
		foreach ($this->headers as $header)
			header($header);
	}
}

?>