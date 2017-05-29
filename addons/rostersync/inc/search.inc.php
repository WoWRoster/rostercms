<?php
/**
 * WoWRoster.net WoWRoster
 *
 *
 * @copyright  2002-2011 WoWRoster.net
 * @license    http://www.gnu.org/licenses/gpl.html   Licensed under the GNU General Public License v3. * @package    MembersList
 * @subpackage Search
 */

if( !defined('IN_ROSTER') )
{
	exit('Detected invalid access to this file!');
}

/**
 * Members List Search
 *
 * @package    MembersList
 * @subpackage Search
 */
class rostersyncSearch
{
	var $options;
	var $result = array();
	var $result_count = 0;
	var $start_search;
	var $stop_search;
	var $time_search;
	var $open_table;
	var $close_table;
	var $search_url;
	var $data = array();    // Addon data

	// class constructor
	function rostersyncSearch()
	{
		global $roster;

		$this->open_table = '<div class="row">
					<div class="col-md-4">Cache Type</div>
					<div class="col-md-4">Name</div>
					<div class="col-md-4">Id</div>
				</div>';
	}

	function search( $search , $limit=10 , $page=0 )
	{
		global $roster;

		$first = $page * $limit;

		$sql = "SELECT * FROM `" . $roster->db->table('api_cache') . "`  WHERE `name` LIKE '%$search%' ORDER BY `name` ASC"
			. ( $limit > 0 ? " LIMIT $first," . $limit : '' ) . ';';

		// calculating the search time
		$this->start_search = format_microtime();

		$result = $roster->db->query($sql);

		$this->stop_search = format_microtime();
		$this->time_search = $this->stop_search - $this->start_search;

		$nrows = $roster->db->num_rows($result);
		$crows = 0;

		$x = ($limit > $nrows) ? $nrows : ($limit > 0 ? $limit : $nrows);
		if( $nrows > 0 )
		{
			while( $x > 0 )
			{
				$row = $roster->db->fetch($result);

				$item['html'] = '<div class="row">
					<div class="col-md-4">'.$row['type'].'</div>
					<div class="col-md-4">'.$row['name'].'</div>
					<div class="col-md-4">'.$row['id'].'</div>
				</div>';
				$this->add_result($item);
				unset($item);

				$x--;
			}
		}
		$roster->db->free_result($result);
	}

	function add_result( $resultarray )
	{
		$this->result[$this->result_count++] = $resultarray;
	}
}
