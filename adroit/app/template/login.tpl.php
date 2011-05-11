<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class tpl_login
{
	public function main()
	{
		if (isset($this->output1))
			echo $this->output1;
		if (isset($this->output2))
			echo $this->output2;
	}
}