<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

function isset_integer($var)
{
	return ((isset($var) && is_int($var)) ? TRUE : FALSE);
}

// Alias of isset_integer
function isset_int($var)
{
	return ((isset($var) && is_int($var)) ? TRUE : FALSE);
}

function isset_float($var)
{
	return ((isset($var) && is_float($var)) ? TRUE : FALSE);
}

function isset_string($var)
{
	return ((isset($var) && is_string($var)) ? TRUE : FALSE);
}

function isset_array($var)
{
	return ((isset($var) && is_array($var)) ? TRUE : FALSE);
}

function isset_object($var)
{
	return ((isset($var) && is_object($var)) ? TRUE : FALSE);
}

function isset_bool($var)
{
	return ((isset($var) && is_bool($var)) ? TRUE : FALSE);
}