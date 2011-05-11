<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class Controller
{
	public $Server;
	public $Get;
	public $Post;
	public $Files;
	public $Cookie;
	public $Session;
	public $Headers;
	
	public $Loader;
	
	public $db;
	public $content;
	
	public function __construct($content = NULL)
	{
		$this->Constants = &$GLOBALS['Constants'];
		$this->Server = &$GLOBALS['Server'];
		$this->Get = &$GLOBALS['Get'];
		$this->Post = &$GLOBALS['Post'];
		$this->Files = &$GLOBALS['Files'];
		$this->Cookie = &$GLOBALS['Cookie'];
		$this->Session = &$GLOBALS['Session'];
		$this->Headers = &$GLOBALS['Headers'];
		
		$this->Loader = &$GLOBALS['Loader'];
		
		if (isset($GLOBALS['db']))
			$this->db = &$GLOBALS['db'];
		
		$this->setContent($content);
	}
	
	public function __destruct()
	{
		if ($this->content !== NULL)
			echo $this->content;
	}
	
	public function setContent($content = NULL)
	{
		if ($content !== NULL)
			$this->content = $content;
	}
	
	public function appendContent($content = NULL)
	{
		if ($content !== NULL)
			$this->content .= $content;
	}
}