<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

/*
	string/int convert12to24( string/int hour, bool pm )
*/
function convert12to24($hour, $pm)
{
	$ihour = intval($hour);
	if ($pm === TRUE && $ihour < 12) // PM hours
		$ihour += 12;
	elseif ($pm === FALSE && $ihour == 12) // Midnight (24:00 => 00:00)
		$ihour = '00';
	// else $ihour remains unchanged
	
	return $ihour;
}
?>