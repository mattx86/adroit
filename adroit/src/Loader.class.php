<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class Loader
{
	private $path;
	private $conf;
	
	private $classes;
	private $libs;
	private $incs;
	
	public function __construct($path = '')
	{
		$this->set_path($path);
		$this->load_config();
	}
	
	public function set_path($path = '')
	{
		// path not specified, auto-detect
		if (empty($path) === TRUE)
		{
			$this->path = getcwd() . '/adroit';
		}
		else
		{
			// strip trailing slash
			if ($path[strlen($path) - 1] == '/')
				$path = substr($path, 0, -1);

			if ($path[0] != '/' && $path[1] != ':') // relative path
				$this->path = getcwd() . '/' . $path;
			else // full path
				$this->path = $path;
		}
	}
	
	private function load_config()
	{
		$this->conf = unserialize(file_get_contents($this->path.'/src/Loader.conf.php'));
		
		$this->classes =& $this->conf['classes'];
		$this->libs =& $this->conf['libs'];
		$this->incs =& $this->conf['incs'];
	}
	
	/********** CLASS FUNCTIONS **********/
	private function class_is_loaded($class_name)
	{
		return $this->classes[$class_name]['loaded'];
	}
	
	private function class_has_depends($class_name)
	{
		return ((!empty($this->classes[$class_name]['depends_on'])) ? TRUE : FALSE);
	}
	
	private function class_load_depends($class_name)
	{
		$this->load_class($this->classes[$class_name]['depends_on']);
	}
	
	private function class_load_file($class_name)
	{
		include_once($this->path.'/'.$this->classes[$class_name]['class_file']);
		$this->classes[$class_name]['loaded'] = TRUE;
	}
	
	public function load_class($class_name)
	{
		if ($this->class_is_loaded($class_name))
			return;
		
		if ($this->class_has_depends($class_name))
			$this->class_load_depends($class_name);
		
		$this->class_load_file($class_name);
	}
	
	/********** LIBRARY FUNCTIONS **********/
	private function lib_is_loaded($lib_name)
	{
		return $this->libs[$lib_name]['loaded'];
	}
	
	private function lib_has_depends($lib_name)
	{
		return ((count($this->libs[$lib_name]['depends_on']) >= 1) ? TRUE : FALSE);
	}
	
	private function lib_load_depends($lib_name)
	{
		$depends =& $this->libs[$lib_name]['depends_on'];
		
		foreach ($depends as $depend)
			$this->load_lib($depend);
	}
	
	private function lib_load_file($lib_name)
	{
		include_once($this->path.'/'.$this->libs[$lib_name]['lib_file']);
		$this->libs[$lib_name]['loaded'] = TRUE;
	}
	
	public function load_lib($lib_name)
	{
		if ($this->lib_is_loaded($lib_name))
			return;
		
		if ($this->lib_has_depends($lib_name))
			$this->lib_load_depends($lib_name);
		
		$this->lib_load_file($lib_name);
	}
	
	/********** INCLUDE FUNCTIONS **********/
	private function inc_is_loaded($inc_name)
	{
		return $this->incs[$inc_name]['loaded'];
	}
	
	private function inc_has_depends($inc_name)
	{
		return ((count($this->incs[$inc_name]['depends_on']) >= 1) ? TRUE : FALSE);
	}
	
	private function inc_load_depends($inc_name)
	{
		$depends =& $this->incs[$inc_name]['depends_on'];
		
		foreach ($depends as $depend)
			$this->load_inc($depend);
	}
	
	private function inc_load_file($inc_name)
	{
		include_once($this->path.'/'.$this->incs[$inc_name]['inc_file']);
		$this->incs[$inc_name]['loaded'] = TRUE;
	}
	
	public function load_inc($inc_name)
	{
		if ($this->inc_is_loaded($inc_name))
			return;
		
		if ($this->inc_has_depends($inc_name))
			$this->inc_load_depends($inc_name);
		
		$this->inc_load_file($inc_name);
	}
	
	/********** HEAD CONTENT FUNCTIONS **********/
	public function get_headcontent($hc_name)
	{
		ob_start();
		eval("?>".implode("", file($this->path.'/app/headcontent/'.$hc_name.'.hc.php')));
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}
}