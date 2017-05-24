<?php
/**
 * WoWRoster.net WoWRoster
 *
 *
 * @copyright  2002-2011 WoWRoster.net
 * @license    http://www.gnu.org/licenses/gpl.html   Licensed under the GNU General Public License v3. * @package    MembersList
 * @subpackage UpdateHook
 */

if ( !defined('IN_ROSTER') )
{
	exit('Detected invalid access to this file!');
}

/**
 * MembersList Update Hook
 *
 * @package    MembersList
 * @subpackage UpdateHook
 */
class memberslistUpdate
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
	/**
	 * Constructor
	 *
	 * @param array $data
	 *		Addon data object
	 */
	function memberslistUpdate($data)
	{
		$this->data = $data;

		include_once($this->data['conf_file']);
	}

	/**
	 * Resets addon messages
	 */
	function reset_messages()
	{
		/**
		 * We display the addon name at the beginning of the output line. If
		 * the hook doesn't exist on this side, nothing is output. If we don't
		 * produce any output (update method off) we empty this before returning.
		 */

		$this->messages = 'memberslist';
	}

	/**
	 * Guild_pre trigger, set a flag if officer note data is not available
	 * - depriceated in roster 3.0
	 * @param array $guild
	 * 		CP.lua guild data
	 */
	function guild_pre($guild)
	{
		global $roster;
		return true;
	}
	/**
	 * Guild trigger, the regex-based alt detection
	 * - depriceated in roster 3.0
	 * @param array $char
	 *		CP.lua guild member data
	 * @param int $member_id
	 * 		Member ID
	 */
	function guild($char, $member_id)
	{
		return true;
	}
	/**
	 * Guild_post trigger: throwing away the old records
	 * - depriceated in roster 3.0
	 * @param array $guild
	 *		CP.lua guild data
	 */
	function guild_post( $guild )
	{
		global $roster;
		return true;
	}

	/**
	 * Char trigger: add the member record to the local data array
	 * - depriceated in roster 3.0
	 * @param array $char
	 *		CP.lua character data
	 * @param int $member_id
	 *		Member ID
	 */
	function char($char, $member_id)
	{
		global $roster;
		return true;
	}

	/**
	 * Char_post trigger: does the actual update.
	 * - depriceated in roster 3.0
	 * @param array $chars
	 *		CP.lua characters data
	 */
	function char_post($chars)
	{
		global $roster;
		return true;
	}
}
