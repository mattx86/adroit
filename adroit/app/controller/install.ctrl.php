<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class ctrl_install extends Controller_Document
{
	public function __construct()
	{
		parent::__construct(XHTML_10_TRANSITIONAL, 'tpl_install', 'main');
		$this->title->appendContent('Install');
	}

	public function GET()
	{
		$output1 = "Installing tables... ";
		$output2 = '';
		if ($this->Users->install_tables() !== FALSE)
		{
			$output1 .= "OK\n";

			$output1 .= "Creating group (Administrator)... ";
			if ($this->Users->add_update_group('Administrator') !== FALSE)
			{
				$output1 .= "OK\n";

				$output1 .= "Creating user (admin)... ";
				if ($this->Users->add_update_user('admin', '12345', 'admin@localhost', FALSE, $this->Users->get_group_id('Administrator')) !== FALSE)
				{
					$output1 .= "OK\n";

					$output1 .= "Activating user (admin)... ";
					if ($this->Users->activate_deactivate_user($this->Users->get_user_id('admin'), TRUE) !== FALSE)
					{
						$output1 .= "OK\n";
					}
					else
					{
						$output1 .= "Failed\n";
					}
				}
				else
				{
					$output1 .= "Failed\n";
				}
			}
			else
			{
				$output1 .= "Failed\n";
			}

		}
		else
			$output1 .= "Failed\n";

		$this->Template->set('output1', $output1);
		$this->Template->set('output2', $output2);
	}
}