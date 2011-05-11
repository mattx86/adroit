<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class URL
{
	public $protocol;
	public $username;
	public $password;
	public $hostname;
	public $port;
	public $path;
	public $get;
	
	public function __toString()
	{
		$protocol = empty($this->protocol) === FALSE ?
			"{$this->protocol}://" : '';
		$userpass = empty($this->username) === FALSE ? $this->username.
			(empty($this->password) !== FALSE ? '@' :
				":{$this->password}@") :
			'';
		$hostname = empty($this->hostname) === FALSE ?
			$this->hostname : '';
		$port = empty($this->port) === FALSE ?
			":{$this->port}" : '';
		$path = '/' . ( empty($this->path) === FALSE ?
			$this->path : '' );
		$get = empty($this->get) === FALSE ?
			$this->get : '';

		return $protocol . $userpass . $hostname . $port .
			$path . $get;
	}
	
	public function set($data)
	{
		$i = 0;
		$get = '';
		foreach ($data as $var => $value)
		{
			if (stripos($var, 'proto') !== FALSE)
				$this->protocol = @$value;
			elseif (stripos($var, 'user') !== FALSE)
				$this->username = @$value;
			elseif (stripos($var, 'pass') !== FALSE)
				$this->password = @$value;
			elseif (stripos($var, 'host') !== FALSE)
				$this->hostname = @$value;
			elseif (stripos($var, 'port') !== FALSE)
				$this->port = @$value;
			elseif (stripos($var, 'path') !== FALSE)
				$this->path = @$value;
			elseif (stripos($var, 'get') !== FALSE)
			{
				foreach ($value as $get_var => $get_value)
				{
					$sep = $i == 0 ? '?' : '&amp;';
					$get .= "{$sep}{$get_var}={$get_value}";
				}
				$this->get = $get;
			}
		}
	}
	
	public function http_absolute($data)
	{
		$data['protocol'] = 'http';
		$this->set($data);
	}
	
	public function https_absolute($data)
	{
		$data['protocol'] = 'https';
		$this->set($data);
	}
}

class Google_Search_URL extends URL
{
	public function __construct($query)
	{
		$this->http_absolute(array(
			'host' => 'www.google.com',
			'path' => 'search',
			'get' => array(
				'q' => $query)
			)
		);
	}
}

?>