<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class Users
{
	protected $db = FALSE;
	protected $table_prefix;
	protected $hash_salt;

	public function __construct (&$db, $table_prefix = '', $hash_salt = 'Adroit')
	{
		if ($db->link === FALSE)
			return;

		$this->db = &$db;
		$this->table_prefix = $table_prefix;
		$this->hash_salt = $hash_salt;
	}

	public function install_tables()
	{
		if ($this->db === FALSE || $this->db->link === FALSE)
			return FALSE;

		$queries[] = <<<EOQ
CREATE TABLE IF NOT EXISTS `{$this->table_prefix}user` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(64) NOT NULL,
  `password` varchar(128) NOT NULL,
  `email_addr` varchar(255) NOT NULL,
  `recv_email` tinyint(1) NOT NULL default '1',
  `group_id` smallint(5) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL default '0',
  `first_login` tinyint(1) NOT NULL default '1',
  `last_login_from` varchar(255) NOT NULL,
  `last_login_time` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `group_id` (`group_id`),
  KEY `active` (`active`),
  KEY `recv_email` (`recv_email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
EOQ;
		$queries[] = <<<EOQ
CREATE TABLE IF NOT EXISTS `{$this->table_prefix}user_bans` (
  `ban_on` enum('username','ip') NOT NULL,
  `ban_value` varchar(255) NOT NULL,
  `permanent` tinyint(1) NOT NULL default '0',
  `attempts` int(10) unsigned NOT NULL,
  `last_attempt` datetime NOT NULL,
  KEY `attempts` (`attempts`),
  KEY `permanent` (`permanent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
EOQ;
		$queries[] = <<<EOQ
CREATE TABLE IF NOT EXISTS `{$this->table_prefix}user_groups` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `groupname` varchar(128) NOT NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `groupname` (`groupname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
EOQ;
		$queries[] = <<<EOQ
CREATE TABLE IF NOT EXISTS `{$this->table_prefix}user_keys` (
  `user_id` int(10) unsigned NOT NULL,
  `key1` varchar(32) NOT NULL,
  `key2` varchar(32) NOT NULL,
  PRIMARY KEY  (`user_id`),
  KEY `key1` (`key1`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
EOQ;
		$queries[] = <<<EOQ
CREATE TABLE IF NOT EXISTS `{$this->table_prefix}user_profiles` (
  `user_id` int(10) unsigned NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `middleinitial` varchar(1) default NULL,
  `lastname` varchar(255) NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
EOQ;

		$this->db->transaction_start();

		foreach ($queries as $query)
		{
			if ($this->db->query($query) === FALSE)
			{
				$this->db->transaction_rollback();
				return FALSE;
			}
		}

		$this->db->transaction_commit();

		return TRUE;
	}

	public function gen_hash($string)
	{
		return hash('whirlpool', $this->hash_salt . $string . $this->hash_salt);
	}

	public function gen_keys()
	{
		$hash = $this->gen_hash(uniqid(mt_rand(),true));
		$keys[] = substr($hash, 0, 8);
		$keys[] = substr($hash, -8, 8);

		return $keys;
	}

	/*** GROUP FUNCTIONS ***/
	public function add_update_group($groupname, $description = '', $group_id = NULL)
	{
		$query[$this->table_prefix.'user_groups']['groupname'] = $groupname;
		$query[$this->table_prefix.'user_groups']['description'] = $description;
		if ($group_id === NULL)
		{
			$result = $this->db->insert($query);
		}
		else
		{
			$query[$this->table_prefix.'user_groups']['WHERE'] = "`id` = {$group_id}";
			$result = $this->db->update($query);
		}

		//echo "<pre>". print_r($result, true). "</pre>";

		return (($result->get_affected_rows($this->table_prefix.'user_groups') == 1) ? TRUE : FALSE);
	}

	public function delete_group($group_id, $fallback_to_group_id = NULL)
	{
		$this->db->query("SELECT `id` FROM `{$this->table_prefix}user` WHERE `group_id` = {$group_id}");

		if ($this->db->result->num_rows > 0)
		{
			if ($fallback_to_group_id === NULL || $fallback_to_group_id === $group_id)
			{
				return FALSE;
			}
			else
			{
				$update[$this->table_prefix.'user']['group_id'] = $fallback_to_group_id;
				$update[$this->table_prefix.'user']['WHERE'] = "`group_id` = {$group_id}";
				$this->db->update($update);

				if ($this->db->result->get_affected_rows($this->table_prefix.'user') != 1)
					return FALSE;
			}
		}

		$delete[$this->table_prefix.'user_groups']['WHERE'] = "`id` = {$group_id}";
		$this->db->delete($delete);

		if ($this->db->result->get_affected_rows($this->table_prefix.'user_groups') != 1)
			return FALSE;

		return TRUE;
	}

	public function get_group_id($groupname)
	{
		$this->db->query("SELECT `id` FROM `{$this->table_prefix}user_groups` WHERE `groupname` = '{$groupname}'");

		if ($this->db->result->num_rows != 1)
			return FALSE;

		return $this->db->result->rows[0]->id;
	}

	// alias of get_group_id
	public function group_exists($groupname)
	{
		return $this->get_group_id($groupname);
	}

	public function get_groupname($group_id)
	{
		$this->db->query("SELECT `groupname` FROM `{$this->table_prefix}user_groups` WHERE `group_id` = {$group_id}");

		if ($this->db->result->num_rows != 1)
			return FALSE;

		return $this->db->result->rows[0]->groupname;
	}

	/*** USER FUNCTIONS ***/
	/*
	  `id` int(10) unsigned NOT NULL auto_increment,
	  `username` varchar(64) NOT NULL,
	  `password` varchar(128) NOT NULL,
	  `email_addr` varchar(255) NOT NULL,
	  `recv_email` tinyint(1) NOT NULL default '1',
	  `group_id` smallint(5) unsigned NOT NULL,
	  `active` tinyint(1) NOT NULL default '0',
	  `first_login` tinyint(1) NOT NULL default '1',
	  `last_login_from` varchar(255) NOT NULL,
	  `last_login_time` datetime NOT NULL,
	*/
	public function add_update_user($username, $password, $email_addr, $recv_email, $group_id, $user_id = NULL)
	{
		$this->db->transaction_start();

		if (!empty($username))
			$user[$this->table_prefix.'user']['username'] = $username;
		if (!empty($password))
			$user[$this->table_prefix.'user']['password'] = $this->gen_hash($password);
		if (!empty($email_addr))
			$user[$this->table_prefix.'user']['email_addr'] = $email_addr;
		if (!empty($recv_email))
			$user[$this->table_prefix.'user']['recv_email'] = intval($recv_email);
		if (!empty($group_id))
			$user[$this->table_prefix.'user']['group_id'] = $group_id;

		if ($user_id === NULL)
		{
			$result = $this->db->insert($user);
		}
		else
		{
			$user[$this->table_prefix.'user']['WHERE'] = "`id` = {$user_id}";
			$result = $this->$db->update($user);
		}

		if ($result->get_affected_rows($this->table_prefix.'user') != 1)
		{
			$this->db->transaction_rollback();
			return FALSE;
		}

		$user_id = $result->get_insert_id($this->table_prefix.'user');
		unset($result);

		if ($user_id === NULL)
		{
			$gen_keys = $this->gen_keys();
			$keys[$this->table_prefix.'user_keys']['user_id'] = $user_id;
			$keys[$this->table_prefix.'user_keys']['key1'] = $gen_keys[0];
			$keys[$this->table_prefix.'user_keys']['key2'] = $gen_keys[1];
			$result = $this->db->insert($keys);

			if ($result->get_affected_rows($this->table_prefix.'user_keys') != 1)
			{
				$this->db->transaction_rollback();
				return FALSE;
			}
			unset($result);

			/*
			  `user_id` int(10) unsigned NOT NULL,
			  `firstname` varchar(255) NOT NULL,
			  `middleinitial` varchar(1) default NULL,
			  `lastname` varchar(255) NOT NULL,
			*/
			$profile[$this->table_prefix.'user_profiles']['user_id'] = $user_id;
			$profile[$this->table_prefix.'user_profiles']['firstname'] = '';
			$profile[$this->table_prefix.'user_profiles']['middleinitial'] = '';
			$profile[$this->table_prefix.'user_profiles']['lastname'] = '';
			$result = $this->db->insert($profile);

			if ($result->get_affected_rows($this->table_prefix.'user_profiles') != 1)
			{
				$this->db->transaction_rollback();
				return FALSE;
			}
			unset($result);
		}

		$this->db->transaction_commit();

		return TRUE;
	}

	public function delete_user($user_id)
	{
			$delete[$this->table_prefix.'user']['WHERE'] = "`id` = {$user_id}";
			$delete[$this->table_prefix.'user_keys']['WHERE'] = "`user_id` = {$user_id}";
			$delete[$this->table_prefix.'user_profiles']['WHERE'] = "`user_id` = {$user_id}";
			$delete[$this->table_prefix.'user_bans']['WHERE'] = "`ban_on` = 'username' AND `ban_value` = '".$this->get_username($user_id)."'";
			$this->db->delete($delete);

			if ($this->db->result->get_affected_rows($this->table_prefix.'user') === 1 && $this->db->result->get_affected_rows($this->table_prefix.'user_profiles') === 1)
				return TRUE;

			return FALSE;
	}

	public function get_user_id($username)
	{
		$result = $this->db->query("SELECT `id` FROM `{$this->table_prefix}user` WHERE `username` = '{$username}'");

		if ($result->num_rows != 1)
			return FALSE;

		return $result->rows[0]->id;
	}

	// alias of get_user_id
	public function user_exists($username)
	{
		return $this->get_user_id($username);
	}

	public function get_username($user_id)
	{
		$this->db->query("SELECT `username` FROM `{$this->table_prefix}user` WHERE `id` = {$user_id}");

		if ($this->db->result->num_rows != 1)
			return FALSE;

		return $this->db->result->rows[0]->username;
	}

	public function get_user_password($user_id)
	{
		$this->db->query("SELECT `password` FROM `{$this->table_prefix}user` WHERE `id` = {$user_id}");

		if ($this->db->result->num_rows != 1)
			return FALSE;

		return $this->db->result->rows[0]->password;
	}

	public function get_user_active($user_id)
	{
		$this->db->query("SELECT `active` FROM `{$this->table_prefix}user` WHERE `id` = {$user_id}");

		if ($this->db->result->num_rows != 1)
			return FALSE;

		return (bool) $this->db->result->rows[0]->active;
	}

	public function get_user_group_id($user_id)
	{
		$this->db->query("SELECT `group_id` FROM `{$this->table_prefix}user` WHERE `id` = {$user_id}");

		if ($this->db->result->num_rows != 1)
			return FALSE;

		return $this->db->result->rows[0]->group_id;
	}

	public function activate_deactivate_user($user_id, $active = TRUE)
	{
		$this->db->transaction_start();

		$update[$this->table_prefix.'user']['active'] = intval($active);
		$update[$this->table_prefix.'user']['WHERE'] = "`id` = {$user_id}";
		$result = $this->db->update($update);

		if ($result->get_affected_rows($this->table_prefix.'user') != 1)
		{
			$this->db->transaction_rollback();
			return FALSE;
		}
		unset($result);

		$delete[$this->table_prefix.'user_keys']['WHERE'] = "`user_id` = {$user_id}";
		$result = $this->db->delete($delete);

		if ($result->get_affected_rows($this->table_prefix.'user_keys') != 1)
		{
			$this->db->transaction_rollback();
			return FALSE;
		}
		unset($result);

		$this->db->transaction_commit();

		return TRUE;
	}

	/*** KEY FUNCTIONS ***/
	public function get_key1($user_id)
	{
		$this->db->query("SELECT `key1` FROM `{$this->table_prefix}user_keys` WHERE `user_id` = {$user_id}");

		if ($this->db->result->num_rows != 1)
			return FALSE;

		return $this->db->result->rows[0]->key1;
	}

	public function get_key2($user_id)
	{
		$this->db->query("SELECT `key2` FROM `{$this->table_prefix}user_keys` WHERE `user_id` = {$user_id}");

		if ($this->db->result->num_rows != 1)
			return FALSE;

		return $this->db->result->rows[0]->key2;
	}

	/*** LOGIN / LOGOUT FUNCTIONS ***/
	public function login($username, $password)
	{
		global $Session;

		if (($user_id = $this->get_user_id($username)) === FALSE)
			return FALSE;

		if ($this->get_user_active($user_id) === FALSE)
			return FALSE;

		if ($this->gen_hash($password) != $this->get_user_password($user_id))
			return FALSE;

		$update[$this->table_prefix.'user']['first_login'] = 0;
		// TODO: Store the last login details in the Session before updating them.
		$update[$this->table_prefix.'user']['last_login_from'] = $_SERVER['REMOTE_ADDR'];
		$update[$this->table_prefix.'user']['last_login_time'] = date('Y-m-d H:i:s');
		$update[$this->table_prefix.'user']['WHERE'] = "`id` = {$user_id}";
		$this->db->update($update);

		$Session->user_id = $user_id;

		return TRUE;
	}

	public function logout()
	{
		global $Session;

		$Session->destroy();
	}
}
