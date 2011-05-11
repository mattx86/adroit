<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class tpl_home
{
	public function main()
	{
			echo 'Logged in as '.$this->username."<br />\n";
			echo "<br />\n";
			echo $this->logout_link;
	}
}