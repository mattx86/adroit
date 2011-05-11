<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class Table extends XHTML_Element
{
	public function __construct ($name_attribute, $content = NULL)
	{
		parent::__construct ('table', $content);
		parent::set_name_and_id ($name_attribute);
	}
}

class TableHeaders extends XHTML_Element
{
	public function __construct ($content = NULL)
	{
		parent::__construct ('thead', $content);
	}
}

class TableBody extends XHTML_Element
{
	public function __construct ($content = NULL)
	{
		parent::__construct ('tbody', $content);
	}
}

class TableRow extends XHTML_Element
{
	public function __construct ($content = NULL)
	{
		parent::__construct ('tr', $content);
	}
}

class TableHeader extends XHTML_Element
{
	public function __construct ($content = NULL)
	{
		parent::__construct ('th', $content);
	}
}

class TableData extends XHTML_Element
{
	public function __construct ($content = NULL)
	{
		parent::__construct ('td', $content);
	}
}

?>