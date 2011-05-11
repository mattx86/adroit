<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class Data_Table extends Table_Enhanced
{
	protected $dbObject_Available = FALSE;
	
	public function __construct ($table_name, $query, &$dbObject)
	{
		parent::__construct ($table_name);
		
		if ($dbObject->link === FALSE)
			return;
		
		$this->dbObject_Available = TRUE;

		$resObject = $dbObject->query ($query);
		$headers = $dbObject->get_field_names ();
		
		parent::set_header ($headers);
		parent::set_data ($resObject->rows);
	}
	
	public function __toString()
	{
		if ($this->dbObject_Available)
			return parent::__toString();
		else
			return '';
	}
}

?>