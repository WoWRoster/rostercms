<?php
/**
 * WoWRoster.net WoWRoster
 *
 * Roster upload rule config
 *
 *
 * @copyright  2002-2011 WoWRoster.net
 * @license    http://www.gnu.org/licenses/gpl.html   Licensed under the GNU General Public License v3.
 * @package    WoWRoster
 * @subpackage RosterCP
*/

if( !defined('IN_ROSTER') || !defined('IN_ROSTER_ADMIN') )
{
	exit('Detected invalid access to this file!');
}

$start = (isset($_GET['start']) ? $_GET['start'] : 0);

$roster->output['title'] .= ' Media';
/*
$(document).ready(function(){

    $(".filter-button").click(function(){
        var value = $(this).attr('data-filter');
        
        if(value == "all")
        {
            //$('.filter').removeClass('hidden');
            $('.filter').show('1000');
        }
        else
        {
//            $('.filter[filter-item="'+value+'"]').removeClass('hidden');
//            $(".filter").not('.filter[filter-item="'+value+'"]').addClass('hidden');
            $(".filter").not('.'+value).hide('3000');
            $('.filter').filter('.'+value).show('3000');
            
        }
    });
    
    if ($(".filter-button").removeClass("active")) {
$(this).removeClass("active");
}
$(this).addClass("active");

});
*/
roster_add_js('templates/' . $roster->tpl->tpl . '/js/jquery.uploadfile.min.js');


	$query = "SELECT * FROM `" . $roster->db->table('media') . "` ;";

	$result = $roster->db->query($query);

	if (!$result)
	{
		die_quietly('Could not fetch buttons from database .  MySQL said: <br />' . $roster->db->error(),'Roster',__FILE__,__LINE__,$query);
	}

	$cat = array();
	while ($row = $roster->db->fetch($result,SQL_ASSOC))
	{
		$cat[$row['post_parent']] = $row['post_parent'];
		//$palet['b' . $row['button_id']] = $row;
		$roster->tpl->assign_block_vars('mideaimages', array(
			'ID'		=> $row['ID'],
			'NAME'		=> $row['post_title'],
			'PARENT'	=> $row['post_parent'],
			'TYPE'		=> $row['post_type'],
			'URL'		=> ROSTER_MEDIA_URL.$row['post_name'].'.'.$row['post_mime_type'],
			'THUMB'		=> ROSTER_MEDIA_URL.$row['post_title'].'-thumb.'.$row['post_mime_type'],
			'FILE'		=> $row['post_name'].'.'.$row['post_mime_type']
		));
	}
	foreach($cat as $c => $d)
	{
		$roster->tpl->assign_block_vars('mideacat', array(
			'ID'		=> $c,
			'NAME'		=> ucfirst($d)
		));
	}
		
		
$roster->tpl->assign_vars(array(
	'IMG_AJAX_LOAD'		=> makelink('ajax-load'),
	'IMG_AJAX_DELETE'	=> makelink('ajax-delete'),
	'IMG_AJAX_UPLOAD'	=> makelink('ajax-upload'),
	)
);

$roster->tpl->set_filenames(array('body' => 'admin/media.html'));
$body = $roster->tpl->fetch('body');