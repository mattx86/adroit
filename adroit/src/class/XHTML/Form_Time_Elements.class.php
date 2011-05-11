<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

/*
Date Functions:
	string monthSelect( string fieldName [, int timestamp ] )
	string daySelect( string fieldName [, int timestamp ] )
	string yearSelect( string fieldName [, int timestamp [, int startYear [, int endYear ]]] )

Time Functions:
	string hourSelect( string fieldName [, int timestamp ] )
	string minuteSelect( string fieldName [, int timestamp ] )
	string ampmSelect( string fieldName [, int timestamp ] )
*/
class MonthSelect extends Select
{
	public function __construct($name, $timestamp = 0)
	{
		if (!$timestamp) { $month = strftime('%m'); } // current month (no timestamp)
		else { $month = strftime('%m', $timestamp); } // month from timestamp
		
		for ($i = 1; $i <= 12; $i++)
		{
			$tmpdate = mktime(0,0,0,$i);
			$optval = strftime('%m', $tmpdate);
			$opttxt = strftime('%B', $tmpdate);
			$options[] = new Option ($optval, $opttxt);
		}

		parent::__construct($name, $options, $month);
	}
}

class DaySelect extends Select
{
	public function __construct($name, $timestamp = 0)
	{
		if (!$timestamp) { $day = strftime('%d'); } // current day (no timestamp)
		else { $day = strftime('%d', $timestamp); } // day from timestamp
		
		for ($i = 1; $i <= 31; $i++)
		{
			$tmpdate = mktime(0,0,0,0,$i);
			$optval = strftime('%d', $tmpdate);
			$options[] = new Option ($optval, $opttxt);
		}
		
		parent::__construct($name, $options, $day);
	}
}

class YearSelect extends Select
{
	public function __construct($name, $timestamp = 0, $startYear = 2, $endYear = 1)
	{
		if (!$timestamp) { $year = strftime('%G'); } // current year (no timestamp)
		else { $year = strftime('%G', $timestamp); } // year from timestamp
		
		$tmpdate = strftime('%G'); // use the current year always; timestamp is just for selection
		$startYear = ($tmpdate - $startYear);
		$endYear = ($tmpdate + $endYear);
		$numYears = ($endYear - $startYear);
		
		for ($i = 0; $i <= $numYears; $i++)
		{
			$thisyear = $startYear + $i;
			$options[] = new Option ($thisyear, $thisyear);
		}
		
		parent::__construct($name, $options, $year);
	}
}

class HourSelect extends Select
{
	public function __construct($name, $timestamp = 0)
	{
		if (!$timestamp) { $hour = strftime('%I'); } // current hour (no timestamp)
		else { $hour = strftime('%I', $timestamp); } // hour from timestamp
		
		for ($i = 1; $i <= 12; $i++)
		{
			$tmpdate = mktime($i);
			$optval = strftime('%I', $tmpdate);
			$options[] = new Option ($optval, $opttxt);
		}
		
		parent::__construct($name, $options, $hour);
	}
}

class MinuteSelect extends Select
{
	public function __construct($name, $timestamp = 0)
	{
		if (!$timestamp) { $minute = strftime('%M'); } // current minute (no timestamp)
		else { $minute = strftime('%M', $timestamp); } // minute from timestamp
		
		for ($i = 0; $i <= 59; $i++)
		{
			$tmpdate = mktime(0,$i);
			$optval = strftime('%M', $tmpdate);
			$options[] = new Option ($optval, $opttxt);
		}
		
		parent::__construct($name, $options, $minute);
	}
}

class AMPMSelect extends Select
{
	public function __construct($name, $timestamp = 0)
	{
		if (!$timestamp) { $hour = strftime('%H'); } // current 24-hour (no timestamp)
		else { $hour = strftime('%H', $timestamp); } // 24-hour from timestamp
		
		$ihour = intval($hour);
		$ampm = (($ihour >= 0 && $ihour <= 11) ? 'AM' : 'PM');
		
		$options[] = new Option('AM', 'AM');
		$options[] = new Option('PM', 'PM');
		
		parent::__construct($name, $options, $ampm);
	}
}

?>