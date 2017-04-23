<?php


class que
{

	var $var1 = '';
	var $qttl = $roster->config['qttl'];
	var $jobttl = $roster->config['jobttl'];
	var $job_id
	
	/*
	*	build the class using the job id if any.
	*
	*
	*/
	
	function __construct($id=null)
	{
		global $roster;
		
		$this->job_id - $id;
		
		
		
		return true;
	}

	/*
	*	This function builds the q from an array created by the addon and returns the job id
	*
	*	var $array - an array of the info the addon supplyed a spacific structure is used and returned
	*			|- @addonname	- the name of the addon for the que
	*			|- @name1		- the first identify name for the que
	*			|- @name2		- see above lol
	*			|- @value1		- 
	*			|- @value2		- 
	*			|- @value3		- 
	*			|- @value4		- 
	*/
	function build_que(array($info))
	{
		global $roster;
		
	}
	
	/*
	*
	*
	*/
	function update_que()
	{
		global $roster;
		
	}
	
	/*
	*
	*
	*/
	function process_que()
	{
		global $roster;
		
	}
	
	/*
	*
	*
	*/
	function finish_que()
	{
		global $roster;
		
	}
}