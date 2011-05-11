<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

// Blueprint CSS
$BPCSS_Screen = new IncludeCSS('css/blueprint/screen.css');
$BPCSS_Screen->media = 'screen, projection';
$BPCSS_Print = new IncludeCSS('css/blueprint/print.css');
$BPCSS_Print->media = 'print';
$BPCSS_IE = new IncludeCSS('css/blueprint/ie.css');
$BPCSS_IE->media = 'screen, projection';

// Initial Document Head Configuration
$this->Document->head->appendContent(
	$BPCSS_Screen .
	$BPCSS_Print .
"\n<!--[if IE]>\n".
	$BPCSS_IE .
"\n<![endif]-->\n".
	new IncludeJS('js/jquery-1.3.1.min.js') .
	new IncludeJS('js/jquery.tablesorter.min.js')
);
$this->JQuery_Document_Ready = new JQuery_Document_Ready;

$this->JQuery_Document_Ready->add('$("input:not(:button):not(:submit):not(:reset)").focus(function() { $(this).parent().parent().addClass("form_on"); $(this).addClass("form_on"); } );');
$this->JQuery_Document_Ready->add('$("input:not(:button):not(:submit):not(:reset)").blur(function() { $(this).parent().parent().removeClass("form_on"); $(this).removeClass("form_on"); } );');
?>