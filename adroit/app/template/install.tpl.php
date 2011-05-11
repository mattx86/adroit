<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class tpl_install
{
	public function main()
	{
		if (isset($this->output1))
			echo "<pre>".$this->output1."</pre>";
		if (isset($this->output2))
			echo $this->output2;
	}
}