<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class Session
{
	public $session_id;
	public $session_name;
	
	// Create a new session, if necessary.
	public function __construct($start_session = FALSE)
	{
		if ($start_session)
			$this->start();
	}
	
	// Variable Handling
	public function __set($session_variable, $session_value)
	{
		$_SESSION[$session_variable] = @serialize($session_value);
	}
	
	public function __get($session_variable)
	{
		return unserialize($_SESSION[$session_variable]);
	}
	
	public function __isset($session_variable)
	{
		return isset($_SESSION[$session_variable]);
	}
	
	// Start a session.
	public function start()
	{
		// Check if session has already been started.
		$this->session_id = @session_id();
		if (empty($this->session_id))
		{
			// Session has not started yet.
			// Start it and store the session ID.
			@session_start();
			$this->session_id = @session_id();
		}
		$this->session_name = @session_name();
	}
	
	// Destroy a session.
	public function destroy()
	{
		// Remove session data.
		$_SESSION = array();
		
		// Destroy session.
		if (isset($_COOKIE[$this->session_name]))
		    setcookie($this->session_name, '', time()-42000, '/');
		session_destroy();
	}
}
