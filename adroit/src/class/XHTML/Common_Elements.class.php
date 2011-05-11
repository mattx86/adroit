<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class Script extends XHTML_Element
{
	public function __construct ($content = '')
	{
		parent::__construct ('script', $content);
	}
}

class JavaScript extends Script
{
	public function __construct ($content = '')
	{
		parent::__construct ($content);
		parent::__set ('type', 'text/javascript');
	}
}

class IncludeJS extends JavaScript
{
	public function __construct ($source)
	{
		parent::__construct ();
		parent::__set ('src', $source);
	}
}

class Style extends XHTML_Element
{
	public function __construct ($content = NULL)
	{
		parent::__construct ('style', $content);
	}
}

class CSS extends Style
{
	public function __construct ($content = NULL)
	{
		parent::__construct ($content);
		parent::__set ('type', 'text/css');
	}
}

class Link extends XHTML_Element
{
	public function __construct ($href)
	{
		parent::__construct ('link');
		parent::__set ('href', $href);
	}
}

class IncludeCSS extends Link
{
	public function __construct ($source)
	{
		parent::__construct ($source);
		parent::__set ('rel', 'stylesheet');
		parent::__set ('type', 'text/css');
	}
}

?>