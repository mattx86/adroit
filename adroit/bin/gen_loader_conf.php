#!/usr/bin/php
<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

// Display usage
if (!isset($argv[1]))
	die("usage: $argv[0] <path to adroit directory>\n\n");

// Display information
echo "Attempting to generate Adroit Loader config.\n";

// Capture source directory input
$src_dir = $argv[1];

// Remove trailing slash in $src_dir, if present.
if ($src_dir[strlen($src_dir)-1] == '/')
	$src_dir = substr($src_dir, 0, strlen($src_dir)-1);

// get_files():
// Returns an array list of files with full paths, filtered by PCRE $filter.
// Ignore the $internal_call variable when using this function.
function get_files($path, $filter, $internal_call = FALSE)
{
	global $src_dir;
	
	$listing = array_diff(scandir($src_dir.'/'.$path), array('.', '..'));
	$dirs = array();
	foreach ($listing as $dir)
	{
		if (is_dir($src_dir.'/'.$path.'/'.$dir))
		{
			$dirs = array_merge($dirs, get_files($path.'/'.$dir, $filter, TRUE));
		}
		else
		{
			$dirs[] = $src_dir.'/'.$path.'/'.$dir;
		}
	}
	
	if ($internal_call === TRUE)
		return $dirs;
	
	return preg_grep($filter, $dirs);
}

// Gather a list of necessary files.
$class_files = get_files('src/class', '/\/([a-zA-Z0-9_\.\ ]+\.[Cc][Ll][Aa][Ss][Ss]\.[Pp][Hh][Pp])$/');
$controller_files = get_files('app/controller', '/\/([a-zA-Z0-9_\.\ ]+\.[Cc][Tt][Rr][Ll]\.[Pp][Hh][Pp])$/');
$template_files = get_files('app/template', '/\/([a-zA-Z0-9_\.\ ]+\.([Tt][Pp][Ll]|[Ff][Oo][Rr][Mm])\.[Pp][Hh][Pp])$/');
$lib_files = get_files('src/lib', '/\/([a-zA-Z0-9_\.\ ]+\.[Ll][Ii][Bb]\.[Pp][Hh][Pp])$/');
$inc_files = get_files('src/inc', '/\/([a-zA-Z0-9_\.\ ]+\.[Ii][Nn][Cc]\.[Pp][Hh][Pp])$/');

// Merge all lists of class files.
$class_files = array_merge($class_files, $controller_files, $template_files);

//print_r($class_files);
//print_r($lib_files);
//print_r($inc_files);

// Build $classes array
$classes = array();
foreach ($class_files as $class_file)
{
	// Load file into an array
	$class_file_contents = file($class_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	
	// Grep it for class definition lines
	$class_def_lines = preg_grep('/^[\ \t]*[Cc][Ll][Aa][Ss][Ss][\ \t]+[^\ \t]+.*$/', $class_file_contents);
	
	// Loop through the class definition lines
	foreach ($class_def_lines as $class_def_line)
	{
		// Store the class name
		$class = trim(preg_replace('/^[\ \t]*[Cc][Ll][Aa][Ss][Ss][\ \t]+([^\ \t]+).*$/', '$1', $class_def_line));
		//echo $class."\n";
		
		// Store possible class dependency
		$class_dep = NULL;
		if (preg_match('/^[\ \t]*[Cc][Ll][Aa][Ss][Ss][\ \t]+[^\ \t]+[\ \t]+[Ee][Xx][Tt][Ee][Nn][Dd][Ss][\ \t]+([^\ \t]+)/', $class_def_line) == 1)
			$class_dep = trim(preg_replace('/^[\ \t]*[Cc][Ll][Aa][Ss][Ss][\ \t]+[^\ \t]+[\ \t]+[Ee][Xx][Tt][Ee][Nn][Dd][Ss][\ \t]+([^\ \t]+)/', '$1', $class_def_line));
			
		/*echo "class [$class]";
		if (!is_null($class_dep))
			echo " extends [$class_dep]";
		echo "\n";*/
		
		// Store class name and properties in the classes array
		$classes[$class]['class_file'] = $class_file;
		$classes[$class]['depends_on'] = $class_dep;
		$classes[$class]['loaded'] = FALSE;
	}
	//echo "\n";
	
	//var_dump($classes);
	//print_r($class_file_contents);
	//die();
}

//var_dump($classes);
//print_r($classes);

// Build $libs array
$libs = array();
foreach ($lib_files as $lib_file)
{
	// Load file into an array
	$lib_file_contents = file($lib_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	
	// Store the short name of the lib file
	$lib_file_short_name = preg_replace('/^[\/a-zA-Z0-9_\-\.\ ]+\/([^\/]+)\.[Ll][Ii][Bb]\.[Pp][Hh][Pp]$/', '$1', $lib_file);
	
	// Grep it for ADROIT: REQUIRES_LIB: lines
	$requires_lib_lines = preg_grep('/^[\ \t]*\/\/[\ \t]*ADROIT:[\ \t]*REQUIRES_LIB:.*$/', $lib_file_contents);
	
	// Loop through the requires lib lines
	unset($lib_dep);
	$lib_dep = array();
	foreach ($requires_lib_lines as $requires_lib_line)
	{
		// Store the required lib name(s)
		$lib_dep_string = trim(preg_replace('/^[\ \t]*\/\/[\ \t]*ADROIT:[\ \t]*REQUIRES_LIB:(.*)$/', '$1', $requires_lib_line));
		//echo "$lib_file_short_name: $lib_dep_string\n";
		
		// Replace seperators with a common separator
		$lib_dep_string = str_replace(' ', ',', $lib_dep_string);
		$lib_dep_string = str_replace(',,', ',', $lib_dep_string);
		
		// Does this line have a separator?
		$lib_dep_string_explode = NULL;
		if (strpos($lib_dep_string, ','))
			$lib_dep_string_explode = explode(',', $lib_dep_string);
		
		// Store the required libs
		if (!is_null($lib_dep_string_explode))
		{
			foreach ($lib_dep_string_explode as $lib_dep_string_explode_part)
				$lib_dep[] = trim($lib_dep_string_explode_part);
		}
		else
			$lib_dep[] = $lib_dep_string;
	}
	
	// Store lib name and properties in the libs array
	$libs[$lib_file_short_name]['lib_file'] = $lib_file;
	$libs[$lib_file_short_name]['depends_on'] = $lib_dep;
	$libs[$lib_file_short_name]['loaded'] = FALSE;
}

//var_dump($libs);
//print_r($libs);

// Build $incs array
$incs = array();
foreach ($inc_files as $inc_file)
{
	// Load file into an array
	$inc_file_contents = file($inc_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	
	// Store the short name of the inc file
	$inc_file_short_name = preg_replace('/^[\/a-zA-Z0-9_\-\.\ ]+\/([^\/]+)\.[Ii][Nn][Cc]\.[Pp][Hh][Pp]$/', '$1', $inc_file);
	
	// Grep it for ADROIT: REQUIRES_INC: lines
	$requires_inc_lines = preg_grep('/^[\ \t]*\/\/[\ \t]*ADROIT:[\ \t]*REQUIRES_INC:.*$/', $inc_file_contents);
	
	// Loop through the requires inc lines
	unset($inc_dep);
	$inc_dep = array();
	foreach ($requires_inc_lines as $requires_inc_line)
	{
		// Store the required inc name(s)
		$inc_dep_string = trim(preg_replace('/^[\ \t]*\/\/[\ \t]*ADROIT:[\ \t]*REQUIRES_INC:(.*)$/', '$1', $requires_inc_line));
		//echo "$inc_file_short_name: $inc_dep_string\n";
		
		// Replace seperators with a common separator
		$inc_dep_string = str_replace(' ', ',', $inc_dep_string);
		$inc_dep_string = str_replace(',,', ',', $inc_dep_string);
		
		// Does this line have a separator?
		$inc_dep_string_explode = NULL;
		if (strpos($inc_dep_string, ','))
			$inc_dep_string_explode = explode(',', $inc_dep_string);
		
		// Store the required incs
		if (!is_null($inc_dep_string_explode))
		{
			foreach ($inc_dep_string_explode as $inc_dep_string_explode_part)
				$inc_dep[] = trim($inc_dep_string_explode_part);
		}
		else
			$inc_dep[] = $inc_dep_string;
	}
	
	// Store inc name and properties in the incs array
	$incs[$inc_file_short_name]['inc_file'] = $inc_file;
	$incs[$inc_file_short_name]['depends_on'] = $inc_dep;
	$incs[$inc_file_short_name]['loaded'] = FALSE;
}

//var_dump($incs);
//print_r($incs);

// Make file paths relative by removing the full path to the source directory.
unset($class, $lib, $inc);
foreach ($classes as $class_name => $class_property)
	$classes[$class_name]['class_file'] = str_replace($src_dir.'/', '', $classes[$class_name]['class_file']);
foreach ($libs as $lib_name => $lib_property)
	$libs[$lib_name]['lib_file'] = str_replace($src_dir.'/', '', $libs[$lib_name]['lib_file']);
foreach ($incs as $inc_name => $inc_property)
	$incs[$inc_name]['inc_file'] = str_replace($src_dir.'/', '', $incs[$inc_name]['inc_file']);

//var_dump($classes);
//var_dump($libs);
//var_dump($incs);

// Make one big array for the Adroit Loader config
$conf = array();
$conf['classes'] = $classes;
$conf['libs'] = $libs;
$conf['incs'] = $incs;

// Serialize and save the $conf array
file_put_contents($src_dir.'/src/Loader.conf.php', serialize($conf));

echo "\nDone.\n\n";