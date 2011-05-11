<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

/*
$routes = array(
	'/^index\/$/', 'ctrl_index',
	'/^aboutus\/$/', array('ctrl_aboutus', 'testfunc')
);
*/
class URLRouter
{
	public static function Route($routes = array(), $http_path = '')
	{
		foreach ($routes as $regex => $do)
		{
			
			$regex = '/^' . str_replace('/', '\/', $http_path) . str_replace('/', '\/', $regex) . '$/';
			$match_count = preg_match_all($regex, $_SERVER['REQUEST_URI'], $route_matches);
			
			if (is_int($match_count) && $match_count >= 1)
			{
				if (is_string($do))
				{
					$obj = new $do;
					$obj->route_matches = $route_matches;
					$obj->$_SERVER['REQUEST_METHOD']();
				}
				elseif (is_array($do))
				{
					list($class, $function) = $do;
					
					$obj = new $class;
					$obj->route_matches = $route_matches;
					$obj->$function();
				}
			}
		}
	}
}