<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class Table_Enhanced extends Table
{
	private $title = NULL;
	private $header = NULL;
	private $data = array ();
	private $footer = NULL;
	
	private $columns = 0;
	private $color;
	
	public function __construct ($name_attribute, $data = array(array()))
	{
		parent::__construct ($name_attribute);
		$this->set_data($data);
	}
	
	public function set_color ($initial, $alternate)
	{
		if (class_exists ('Toggle'))
			$this->color = new Toggle ($initial, $alternate);
	}
	
	public function set_title ($title, $name_attribute = NULL)
	{
		$this->title = new TableHeader ($title);
		$this->title->set_name_and_id ($name_attribute);
	}
	
	public function get_title ()
	{
		if ($this->title !== NULL)
		{
			$this->title->colspan = $this->columns;
			return new TableRow ($this->title);
		}
		
		return '';
	}
	
	public function set_header ($header_columns = array())
	{
		foreach ($header_columns as $header_column)
		{
			$this->header[] = new TableHeader ($header_column);
		}
	}
	
	public function get_header ()
	{
		if ($this->header === NULL)
			return '';
		
		$header_str = '';
		foreach ($this->header as $header)
		{
			$header_str .= $header;
		}
		
		$tr = new TableRow ($header_str);
		
		return new TableHeaders ($tr);
	}
	
	public function set_data ($data = array (array ()) )
	{
		foreach ($data as $row_key => $row)
		{
			foreach ($row as $column)
			{
				$this->data[$row_key][] = new TableData ($column);
			}
		}
	}
	
	public function get_data ()
	{
		$tr = new TableRow ();
		
		$row_str = '';
		foreach ($this->data as $row)
		{
			$column_str = '';
			foreach ($row as $column)
			{
				$column_str .= $column;
			}
			
			$tr->setContent ($column_str);
			if ($this->color instanceof Toggle)
				$tr->bgcolor = $this->color;
			
			$row_str .= $tr;
		}
				
		return new TableBody ($row_str);
	}
	
	public function set_footer ($footer, $name_attribute = NULL)
	{
		$this->footer = new TableHeader ($footer);
		$this->footer->set_name_and_id ($name_attribute);
	}
	
	public function get_footer ()
	{
		if ($this->footer !== NULL)
		{
			$this->footer->colspan = $this->columns;
			return new TableRow ($this->footer);
		}
		
		return '';
	}
	
	public function __toString ()
	{
		$this->columns = count ($this->header);
		
		parent::setContent ($this->get_title () .
			$this->get_header () .
			$this->get_data () .
			$this->get_footer ()
		);
		
		return parent::__toString();
	}
	
	public function render ()
	{
		echo $this->__toString();
	}
}

?>