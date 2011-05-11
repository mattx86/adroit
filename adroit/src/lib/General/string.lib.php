<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

function subtok($string, $chr, $pos, $len = NULL)
{
  return implode($chr, array_slice(explode($chr,$string), $pos, $len));
}

?>