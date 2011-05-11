<?php
/*
The CSS and (X)HTML contained herein are:

Copyright (c) 2005-2007 Stu Nicholls. All rights reserved.
This stylesheet and the associated (x)html may be modified in any 
way to fit your requirements.


The PHP contained herein is:

Copyright (c) 2007, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class VerticalMenu
{
	protected $menu_data;
	
	public function __construct($menu_data = NULL)
	{
		if ($menu_data !== NULL)
			$this->setContent($menu_data);
	}
	
	public function setContent($menu_data)
	{
		$this->menu_data = $menu_data;
	}
	
	public function appendContent($menu_data)
	{
		/**
		 * @todo write this function
		 */
	}
	
	public static function getCSS()
	{
		return
'/* ================================================================ 
This copyright notice must be untouched at all times.

The original version of this stylesheet and the associated (x)html
is available at http://www.cssplay.co.uk/menus/simple_vertical.html
Copyright (c) 2005-2007 Stu Nicholls. All rights reserved.
This stylesheet and the associated (x)html may be modified in any 
way to fit your requirements.
=================================================================== */

/* Add a margin - for this demo only - and a relative position with a high z-index to make it appear over any element below
   Original (Matt): margin:25px 0 100px 15px; */
#menu_container {margin: 0; padding: 0; position:relative; width:735px; height:25px; z-index:100;}

/* Get rid of the margin, padding and bullets in the unordered lists */
#pmenu, #pmenu ul {padding:0; margin:0; list-style-type: none;}

/* Set up the link size, color and borders */
#pmenu a, #pmenu a:visited {display:block;width:120px; font-size:11px; color:#fff; height:25px; line-height:24px; text-decoration:none; text-indent:5px; border:1px solid #000; border-width:1px 0 1px 1px;}

/* Set up the sub level borders */
#pmenu li ul li a, #pmenu li ul li a:visited {border-width:0 1px 1px 1px;}
#pmenu li a.enclose, #pmenu li a.enclose:visited {border-width:1px;}

/* Set up the list items */
#pmenu li {float:left; background:#7484ad;}

/* For Non-IE browsers and IE7 */
#pmenu li:hover {position:relative;}
/* Make the hovered list color persist */
#pmenu li:hover > a {background:#dfd7ca; color:#c00;}
/* Set up the sublevel lists with a position absolute for flyouts and overrun padding. The transparent gif is for IE to work */
#pmenu li ul {display:none;}
/* For Non-IE and IE7 make the sublevels visible on list hover. This is all it needs */
#pmenu li:hover > ul {display:block; position:absolute; top:-11px; left:80px; padding:10px 30px 30px 30px; background:transparent url(transparent.gif); width:120px;}
/* Position the first sub level beneath the top level liinks */
#pmenu > li:hover > ul {left:-30px; top:16px;}

/* get rid of the table */
#pmenu table {position:absolute; border-collapse:collapse; top:0; left:0; z-index:100; font-size:1em;}

/* For IE5.5 and IE6 give the hovered links a position relative and a change of background and foreground color. This is needed to trigger IE to show the sub levels */
* html #pmenu li a:hover {position:relative; background:#dfd7ca; color:#c00;}

/* For accessibility of the top level menu when tabbing */
#pmenu li a:active, #pmenu li a:focus {background:#dfd7ca; color:#c00;}

/* Set up the pointers for the sub level indication */
#pmenu li.fly {background:#7484ad url(fly.gif) no-repeat right center;}
#pmenu li.drop {background:#7484ad url(drop.gif) no-repeat right center;}


/* This lot is for IE5.5 and IE6 ONLY and is necessary to make the sublevels appear */

/* change the drop down levels from display:none; to visibility:hidden; */
* html #pmenu li ul {visibility:hidden; display:block; position:absolute; top:-11px; left:80px; padding:10px 30px 30px 30px; background:transparent url(transparent.gif);}

/* keep the third level+ hidden when you hover on first level link */
#pmenu li a:hover ul ul{
visibility:hidden;
}
/* keep the fourth level+ hidden when you hover on second level link */
#pmenu li a:hover ul a:hover ul ul{
visibility:hidden;
}
/* keep the fifth level hidden when you hover on third level link */
#pmenu li a:hover ul a:hover ul a:hover ul ul{
visibility:hidden;
}
/* keep the sixth level hidden when you hover on fourth level link */
#pmenu li a:hover ul a:hover ul a:hover ul a:hover ul ul {
visibility:hidden;
}

/* make the second level visible when hover on first level link and position it */
#pmenu li a:hover ul {
visibility:visible; left:-30px; top:14px; lef\t:-31px; to\p:15px;
}

/* make the third level visible when you hover over second level link and position it and all further levels */
#pmenu li a:hover ul a:hover ul{ 
visibility:visible; top:-11px; left:80px;
}
/* make the fourth level visible when you hover over third level link */
#pmenu li a:hover ul a:hover ul a:hover ul { 
visibility:visible;
}
/* make the fifth level visible when you hover over fourth level link */
#pmenu li a:hover ul a:hover ul a:hover ul a:hover ul { 
visibility:visible;
}
/* make the sixth level visible when you hover over fifth level link */
#pmenu li a:hover ul a:hover ul a:hover ul a:hover ul a:hover ul { 
visibility:visible;
}
/* If you can see the pattern in the above IE5.5 and IE6 style then you can add as many sub levels as you like */

';
	}
	
	public function __toString()
	{
		$L1i = 0;
		$L2i = 0;
		$L3i = 0;
		$menu = <<<EOF
<div id="menu_container">
<ul id="pmenu">

EOF;
		if (count($this->menu_data))
		{
			foreach ($this->menu_data as $L1)
			{
				$L1i++;
				
				if (count($L1) > 1)
				{
					foreach ($L1 as $L2)
					{
						$L2i++;
						
						if (count($L2) > 1) // level three
						{
							foreach ($L2 as $L3)
							{
								$L3i++;

								unset($tmp);
								$tmp = explode('|', $L3);

								if ($L3i == 1) // level one for level two drop down
								{
									//$tmp2 = ($L2i == count($L1))?' class="enclose"':'';
									$tmp2 = '';
									$menu .= <<<EOF
	<li class="fly"><a href="{$tmp[1]}"{$tmp2}>{$tmp[0]}<!--[if IE 7]><!--></a><!--<![endif]-->
		<!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul>

EOF;
								}
								else
								{
									$tmp2 = ($L3i == 2)?' class="enclose"':'';
									$menu .= "\t\t<li><a href=\"{$tmp[1]}\"{$tmp2}>{$tmp[0]}</a></li>\n";

									if ($L3i == count($L2)) // end of level three
									{
										$L3i = 0;
										$menu .= <<<EOF
		</ul>
		<!--[if lte IE 6]></td></tr></table></a><![endif]-->
		</li>

EOF;
									}
								}
							}

							if ($L2i == count($L1)) // end of level two [level 3]
							{
								$L2i = 0;
								$menu .= <<<EOF
	</ul>
	<!--[if lte IE 6]></td></tr></table></a><![endif]-->
	</li>

EOF;
							}
						}
						else // level two
						{
							unset($tmp);
							$tmp = explode('|', $L2);

							if ($L2i == 1) // level one for level two drop down
							{
								$tmp2 = ($L1i == count($this->menu_data))?' class="enclose"':'';
								$menu .= <<<EOF
<li class="drop"><a href="{$tmp[1]}"{$tmp2}>{$tmp[0]}<!--[if IE 7]><!--></a><!--<![endif]-->
	<!--[if lte IE 6]><table><tr><td><![endif]-->
	<ul>

EOF;
							}
							else
							{
								$tmp2 = ($L2i == 2)?' class="enclose"':'';
								$menu .= "\t<li><a href=\"{$tmp[1]}\"{$tmp2}>{$tmp[0]}</a></li>\n";

								if ($L2i == count($L1)) // end of level two
								{
									$L2i = 0;
									$menu .= <<<EOF
	</ul>
	<!--[if lte IE 6]></td></tr></table></a><![endif]-->
	</li>

EOF;
								}
							}
						}
					}
				}
				else // level one
				{
					unset($tmp);
					$tmp = explode('|', $L1);
					$tmp2 = ($L1i == count($this->menu_data))?' class="enclose"':'';
					$menu .= "<li><a href=\"{$tmp[1]}\"{$tmp2}>{$tmp[0]}</a></li>\n";
				}
			}
		}

		$menu .= <<<EOF
</ul>
</div>

EOF;

		return $menu;
	}
}
?>