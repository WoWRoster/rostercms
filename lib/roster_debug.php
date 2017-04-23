<?php

/**
 * Roster Error Handler
 *
 * @package    WoWRoster
 * @subpackage ErrorControl
 */
class roster_debug
{
	
	var $level;
	var $debugmessages = array();
	var $log;
	function roster_debug()
	{
		define('DEBUG_STARTTIME', isset($_POST['DEBUG_STARTTIME']) ? $_POST['DEBUG_STARTTIME']: format_microtime());
	}
	
	function _debug( $level = 0, $ret = false, $info = false, $status = false )
	{
		global $roster, $addon;

		$timestamp = round((format_microtime() - DEBUG_STARTTIME), 4);
		if( version_compare(phpversion(), '4.3.0','>=') ) {
			$tmp = debug_backtrace();
			$trace = $tmp[1];
		}
		
		$array = array(
			'time' => $timestamp,
			'file' => isset($trace['file']) ? str_replace($addon['dir'], '', $trace['file']) : 'ApiSync.class.php',
			'line' => isset($trace['line']) ? $trace['line'] : '',
			'function' => isset($trace['function']) ? $trace['function'] : '',
			'class' => isset($trace['class']) ? $trace['class'] : '',
			//'object' => isset($trace['object']) ? $trace['object'] : '',
			//'type' => isset($trace['type']) ? $trace['class'] : '',
			'args' => ( $addon['config']['rsync_debugdata'] != 0 && isset($trace['args']) && !is_object($trace['args']) ) ? $trace['args'] : '',
			'ret' => ( $addon['config']['rsync_debugdata'] != 0 && isset($ret) && !is_object($ret)) ? $ret : '',
			'info' => isset($info) ? $info : '',
			'status' => isset($status) ? $status : '',
		);
		
        $this->debugmessages[] = $array;
		
    }
	
	function get_debug()
	{
		global $roster, $addon;
		
		if (count($this->debugmessages) > 0)
		{
			if ($roster->switch_row_class(false) != 1 )
			{
				$roster->switch_row_class();
			}
			
			foreach ( $this->debugmessages as $message )
			{
				$roster->tpl->assign_block_vars('d_row', array(
				'FILE' => $message['file'],
				'LINE' => $message['line'],
				'TIME' => $message['time'],
				'CLASS' => $message['class'],
				'FUNC' => $message['function'],
				'INFO' => $message['info'],
				'STATUS' => $message['status'],
				'ARGS' => aprint($message['args'], '', 1),
				'RET'  => aprint($message['ret'], '' , 1),
				'ROW_CLASS1' => $addon['config']['rsync_debugdata'] ? 1 : $roster->switch_row_class(),
				'ROW_CLASS2' => 1,
				'ROW_CLASS3' => 1,
				));
			}
			return true;
		}
		return false;
		
	}
	
}