<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class ctrl_login extends Controller_Document
{
	public function __construct()
	{
		parent::__construct(XHTML_10_TRANSITIONAL, 'tpl_login', 'main');
		$this->title->appendContent('Login');
	}

	public function GET()
	{
		$this->display_login_form();
	}

	public function POST()
	{
		$this->login($this->Post->user, $this->Post->pass);
	}

	public function display_login_form()
	{
		$form_login1 = new Form('form_login1', APP_HTTP_PATH.'/login/', 'POST');
		$this->Template->set('output1', $form_login1);
	}

	public function login($user, $pass)
	{
		$output1 .= "Logging in as {$user}... ";
		if ($this->Users->login($user, $pass) !== FALSE)
		{
			$output1 .= "OK<br /><br />\n";
			$output2 = '<a href="'.APP_HTTP_PATH.'/">Continue</a>';
		}
		else
		{
			$output1 .= "Failed\n";
		}
		
		$this->Template->set('output1', $output1);
		if (!empty($output2))
			$this->Template->set('output2', $output2);
	}

	public function logout()
	{
		$this->Session->destroy();
		$this->Template->set('output1', "Logged out<br /><br />\n");
		$this->Template->set('output2', '<a href="'.APP_HTTP_PATH.'/">Continue</a>');
	}
}
