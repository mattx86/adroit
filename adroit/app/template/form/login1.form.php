<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class form_login1
{
    public function main()
    {
		$username_text = new Text('user');
		$username_label = new Label($username_text, 'Username: ');
		$username_label->style = 'font-weight: bold;';
		$password_text = new Text('pass');
		$password_label = new Label($password_text, 'Password: ');
		$password_label->style = 'font-weight: bold;';
		$submit_button = new Submit('submit', 'Login');
		
		$form_table = new Table_Enhanced('form_table');
		$form_table->set_title(APP_NAME.': Login');
		$form_table->set_data(array(
			array($username_label, $username_text),
			array($password_label, $password_text),
			array('', $submit_button)
		));
		$form_table->style = 'border-collapse: collapse;';
		
		echo $form_table;
		echo '<br />Current time is: [<strong>'.date('l, F jS, Y h:i:s A').'</strong>]';
    }
}