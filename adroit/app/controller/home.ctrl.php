<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class ctrl_home extends Controller_Document
{
	public function __construct()
	{
		parent::__construct(XHTML_10_TRANSITIONAL, 'tpl_home', 'main');
		$this->title->appendContent('Home');
	}
	
	public function GET()
	{
		$this->Template->set('username', $this->Users->get_username($this->Session->user_id));
		$this->Template->set('logout_link', '<a href="'.APP_HTTP_PATH.'/logout/">Logout</a>');
	}
}