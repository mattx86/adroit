<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class Controller_Document extends Document
{
	public $Server;
	public $Get;
	public $Post;
	public $Files;
	public $Cookie;
	public $Session;
	public $Headers;
	public $Users;
	
	public $Loader;
	
	public $db;
	public $Template;
	
	public function __construct($doctype = XHTML_10_TRANSITIONAL, $template = NULL, $headcontent = NULL)
	{
		parent::__construct($doctype);
		$this->beautified = APP_BEAUTIFY;
		$this->set_language($GLOBALS['app_language_short']);
		$this->set_content_type(APP_CONTENT_TYPE, $GLOBALS['app_charset']);
		$this->title->setContent(APP_NAME . ': ');
		
		$this->Constants = &$GLOBALS['Constants'];
		$this->Server = &$GLOBALS['Server'];
		$this->Get = &$GLOBALS['Get'];
		$this->Post = &$GLOBALS['Post'];
		$this->Files = &$GLOBALS['Files'];
		$this->Cookie = &$GLOBALS['Cookie'];
		$this->Session = &$GLOBALS['Session'];
		$this->Headers = &$GLOBALS['Headers'];
		$this->Users = &$GLOBALS['users'];
		
		$this->Loader = &$GLOBALS['Loader'];
		
		if (isset($GLOBALS['db']))
			$this->db = &$GLOBALS['db'];
		
		if ($template !== NULL)
			$this->Template = new Template($template);
		
		if ($headcontent !== NULL)
			$this->head->appendContent($this->Loader->get_headcontent($headcontent));
	}
	
	public function __destruct()
	{
		if ($this->Template !== NULL)
			$this->body->setContent($this->Template);
		
		$this->render();
	}
}

?>