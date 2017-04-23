<?php


if ( !defined('IN_ROSTER') )
{
	exit('Detected invalid access to this file!');
}


class userUpdate
{
	// Update messages
	var $messages = '';

	// Addon data object, recieved in constructor
	var $data;

	// LUA upload files accepted. We don't use any.
	var $files = array();

	// Character data cache
	var $chars = array();

	// Officer note check. Default true, because manual update bypasses the check.
	var $passedCheck=true;
	var $assignstr = array();
	var $guild_id = '';
	/**
	 * Constructor
	 *
	 * @param array $data
	 *		Addon data object
	 */
	function userUpdate($data)
	{
		$this->data = $data;
	}

	function reset_messages()
	{
		/**
		 * We display the addon name at the beginning of the output line. If
		 * the hook doesn't exist on this side, nothing is output. If we don't
		 * produce any output (update method off) we empty this before returning.
		 */

		$this->messages = 'user: ';
	}
}
