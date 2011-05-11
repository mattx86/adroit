<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class Toggle
{
	public $intial;
	public $alternate;
	
	protected $current;
	
	public function __construct ($initial, $alternate)
	{
		$this->initial = @$initial;
		$this->alternate = @$alternate;
	}
	
	public function __toString ()
	{
		if ( isset ($this->current) === FALSE ||
		     ( isset ($this->current) === TRUE &&
		       $this->current == $this->alternate ) )
			$this->current = $this->initial;
		else
			$this->current = $this->alternate;
		
		return $this->current;
	}
}
?>