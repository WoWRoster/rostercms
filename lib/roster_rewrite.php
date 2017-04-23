<?php


class rewrite {
	
	var $rules;
	
	function rewrite()
	{
		global $roster;
		
		$sqlquery2 = "SELECT * FROM `".$roster->db->table('rewrite')."`";
		$result2 = $roster->db->query($sqlquery2);
		//$this->rules = $roster->db->fetch($result2);
		while($r = $roster->db->fetch($result2))
		{
			$this->rules[$r['url']] = $r;
		}
	}
	
	function add_rule($url)
	{
		global $roster;
		/*
		echo '<pre>';
		$url .'<br>';
		
		list($base, $query) = explode('&',$url);
		
		echo $base .' - '.$query.'<br>';
		print_r(parse_url($url));
		echo '</pre><br><hr><br>';
		*/
		
	}
}