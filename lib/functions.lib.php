<?php
/**
 * WoWRoster.net WoWRoster
 *
 * Common functions for Roster
 *
 * @copyright  2002-2011 WoWRoster.net
 * @license    http://www.gnu.org/licenses/gpl.html   Licensed under the GNU General Public License v3.
 * @package    WoWRoster
 */

if( !defined('IN_ROSTER') )
{
	exit('Detected invalid access to this file!');
}

// Global variables this file uses

// Index to generate unique toggle IDs
$toggleboxes = 0;
// Array of Tooltips
$tooltips = array();

/**
 * Makes a tootip and places it into the tooltip array
 *
 * @param string $var
 * @param string $content
 */
function _makeslug($string)
{
	$string = strtolower( $string );
	$string = str_replace(' ','_',$string);
	return $string;
}
 
/**
 * Makes a tootip and places it into the tooltip array
 *
 * @param string $var
 * @param string $content
 */
function setTooltip( $var , $content )
{
	global $tooltips;

	if( !isset($tooltips[$var]) )
	{
		$content = str_replace("\n",'',$content);
		$content = addslashes($content);
		$content = str_replace('</','<\\/',$content);
		$content = str_replace('/>','\\/>',$content);

		$tooltips += array($var=>$content);
	}
}

/**
 * Gathers all tootips and places them into javascript variables
 *
 * @param array $tooltipArray
 * @return string Tooltips placed in javascript variables
 */
function getAllTooltips( )
{
	global $tooltips;

	if( is_array($tooltips) )
	{
		$ret_string = array();
		foreach ($tooltips as $var => $content)
		{
			$ret_string[] = 'var overlib_'. $var .' = "' . str_replace('--', '-"+"-', $content) . '";';
		}

		return implode("\n", $ret_string);
	}
	else
	{
		return '';
	}
}

/**
* Highlight certain keywords in a SQL query
*
* @param string $sql Query string
* @return string Highlighted string
*/
function sql_highlight( $sql )
{
	global $roster;

	// Make table names bold
	$sql = preg_replace('/' . $roster->db->prefix . '(\S+?)([\s\.,]|$)/', '<span class="blue">' . $roster->db->prefix . "\\1\\2</span>", $sql);

	// Non-passive keywords
	$red_keywords = array('/(INSERT INTO)/','/(UPDATE\s+)/','/(DELETE FROM\s+)/','/(CREATE TABLE)/','/(IF (NOT)? EXISTS)/',
						  '/(ALTER TABLE)/', '/(CHANGE)/','/(SET)/','/(REPLACE INTO)/');

	$red_replace = array_fill(0, sizeof($red_keywords), '<span class="red">\\1</span>');
	$sql = preg_replace( $red_keywords, $red_replace, $sql );


	// Passive keywords
	$green_keywords = array('/(SELECT)/','/(FROM)/','/(WHERE)/','/(LIMIT)/','/(ORDER BY)/','/(GROUP BY)/',
							'/(\s+AND\s+)/','/(\s+OR\s+)/','/(\s+ON\s+)/','/(BETWEEN)/','/(DESC)/','/(LEFT JOIN)/','/(SHOW TABLES)/',
							'/(LIKE)/','/(PRIMARY KEY)/','/(VALUES)/','/(TYPE)/','/(ENGINE)/','/(MyISAM)/','/(SHOW COLUMNS)/');

	$green_replace = array_fill(0, sizeof($green_keywords), '<span class="green">\\1</span>');
	$sql = preg_replace( $green_keywords, $green_replace, $sql );

	return $sql;
}

/**
 * Clean replacement for die(), outputs a message with debugging info if needed and ends output
 *
 * @param string $text Text to display on error page
 * @param string $title Title to place on web page
 * @param string $file Filename to display
 * @param string $line Line in file to display
 * @param string $sql Any SQL text to display
 */
function die_quietly( $text='' , $title='Message' , $file='' , $line='' , $sql='' )
{
	global $roster;

	if( $roster->pages[0] == 'ajax' )
	{
		ajax_die($text, $title, $file, $line, $sql);
	}

	// Set scope to util
	$roster->scope = 'util';

	// die_quitely died quietly
	if(defined('ROSTER_DIED') )
	{
		echo "<pre>The quiet die function suffered a fatal error. Die information below\n";
		echo "First die data:\n";
		print_r($GLOBALS['die_data']);
		echo "\nSecond die data:\n";
		print_r(func_get_args());
		if( !empty($roster->error->report) )
		{
			echo "\nPHP Notices/Warnings:\n";
			print_r( $roster->error->report );
		}
		exit();
	}

	define( 'ROSTER_DIED', true );

	$GLOBALS['die_data'] = func_get_args();

	$roster->output['title'] = $title;

	if( !defined('ROSTER_HEADER_INC') && is_array($roster->config) )
	{
		include_once(ROSTER_BASE . 'header.php');
	}

	if( !defined('ROSTER_MENU_INC') && is_array($roster->config) )
	{
		$roster_menu = new RosterMenu;
		$roster_menu->makeMenu($roster->output['show_menu']);
		$roster_menu->displayMenu();
	}

	// Only print the border if we have any information
	if( !empty($text) && !empty($title) && !empty($file) && !empty($line) && !empty($sql) )
	{
		echo border('sred','start',$title) . '<table cellspacing="0" cellpadding="0">'."\n";

		if( !empty($text) )
		{
			echo "<tr>\n<td class=\"membersRow1\" style=\"white-space:normal;\"><div style=\"text-align:center;\">$text</div></td>\n</tr>\n";
		}
		if( !empty($sql) )
		{
			echo "<tr>\n<td class=\"membersRow1\" style=\"white-space:normal;\">SQL:<br />" . sql_highlight($sql) . "</td>\n</tr>\n";
		}
		if( !empty($file) )
		{
			$file = str_replace(ROSTER_BASE,'',$file);

			echo "<tr>\n<td class=\"membersRow1\">File: $file</td>\n</tr>\n";
		}
		if( !empty($line) )
		{
			echo "<tr>\n<td class=\"membersRow1\">Line: $line</td>\n</tr>\n";
		}

		if( $roster->config['debug_mode'] == 2 )
		{
			echo "<tr>\n<td class=\"membersRow1\" style=\"white-space:normal;\">";
			echo  APrint::backtrace();
			echo "</td>\n</tr>\n";
		}

		echo "</table>\n" . border('sred','end');
	}

	if( !defined('ROSTER_FOOTER_INC') && is_array($roster->config) )
	{
		include_once(ROSTER_BASE . 'footer.php');
	}

	if( is_object($roster->db) )
	{
		$roster->db->close_db();
	}

	exit();
}

/**
 * Draw a message box with the specified border color, then die cleanly
 *
 * @param string $text | The message to display inside the box
 * @param string $title | The box title (default = 'Message')
 * @param string $style | The border style (default = sred)
 */
function roster_die( $text , $title = 'Message' , $style = 'sred' )
{
	global $roster;

	if( $roster->pages[0] == 'ajax' )
	{
		ajax_die($text, $title, null, null, null );
	}

	// Set scope to util
	$roster->scope = 'util';

	if( !defined('ROSTER_MENU_INC') && is_array($roster->config) )
	{
		$roster_menu = new RosterMenu;
		$roster_menu->makeMenu($roster->output['show_menu']);
	}

	if( !defined('ROSTER_HEADER_INC') && is_array($roster->config) )
	{
		include_once(ROSTER_BASE . 'header.php');
	}

	$roster_menu->displayMenu();

	//echo messagebox($text, $title, $style);
	roster_404();

	if( !defined('ROSTER_FOOTER_INC') && is_array($roster->config) )
	{
		include_once(ROSTER_BASE . 'footer.php');
	}

	if( is_object($roster->db) )
	{
		$roster->db->close_db();
	}

	exit();
}

/**
 * Print a roster-ajax XML error message
 */
function ajax_die($text, $title, $file, $line, $sql)
{
	if( $file )
	{
		$text .= "\n" . 'FILE: ' . $file;
	}
	if( $line )
	{
		$text .= "\n" . 'LINE: ' . $line;
	}
	if( $sql )
	{
		$text .= "\n" . 'SQL: ' . $sql;
	}
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
		. "<response>\n"
		. "  <method/>\n"
		. "  <cont/>\n"
		. "  <result/>\n"
		. "  <status>255</status>\n"
		. "  <errmsg>" . $text . "</errmsg>\n"
		. "</response>\n";
	exit();
}


/**
 * Print a debug backtraceusing aprint::backtrace().
 */
function backtrace()
{
	return APrint::backtrace();
}

/**
 * This will remove HTML tags, javascript sections and white space
 * It will also convert some common HTML entities to their text equivalent
 *
 * @param string $file
 */
function stripAllHtml( $string )
{
	$search = array ('@<script[^>]*?>.*?</script>@si', // Strip out javascript
					'@<[\/\!]*?[^<>]*?>@si',           // Strip out HTML tags
					'@([\r\n])[\s]+@',                 // Strip out white space
					'@&(quot|#34);@i',                 // Replace HTML entities
					'@&(amp|#38);@i',
					'@&(lt|#60);@i',
					'@&(gt|#62);@i',
					'@&(nbsp|#160);@i',
					'@&(iexcl|#161);@i',
					'@&(cent|#162);@i',
					'@&(pound|#163);@i',
					'@&(copy|#169);@i',
					'@&#(\d+);@e');                    // evaluate as php

	$replace = array ('','',"\n",'"','&','<','>',' ',chr(161),chr(162),chr(163),chr(169),'chr(\1)');

	$string = preg_replace($search, $replace, $string);

	return $string;
}

/**
 * This will check if the given Filename is an image
 *
 * @param imagefile $file
 * @return mixed The extentsion if the filetype is an image, false if it is not
 */
function check_if_image( $imagefilename )
{
	if( ($extension = pathinfo($imagefilename, PATHINFO_EXTENSION)) === FALSE )
	{
		return false;
	}
	else
	{
		switch( $extension )
		{
			case 'bmp': 	return $extension;
			case 'cod': 	return $extension;
			case 'gif': 	return $extension;
			case 'ief': 	return $extension;
			case 'jpg': 	return $extension;
			case 'jpeg': 	return $extension;
			case 'jfif': 	return $extension;
			case 'tif': 	return $extension;
			case 'ras': 	return $extension;
			case 'ico': 	return $extension;
			case 'pnm': 	return $extension;
			case 'pbm': 	return $extension;
			case 'pgm': 	return $extension;
			case 'ppm': 	return $extension;
			case 'rgb': 	return $extension;
			case 'xwd': 	return $extension;
			case 'png': 	return $extension;
			case 'jps': 	return $extension;
			case 'fh': 		return $extension;

			default: 		return false;
		}
	}
}

/**
 * Tooltip colorizer function with string cleaning
 * Use only with makeOverlib
 *
 * @param string $tooltip | Tooltip as a string (delimited by "\n" character)
 * @param string $caption_color | (optional) Color for the caption
 * Default is 'ffffff' - white
 * @param string $locale | (optional) Locale so color parser can work correctly
 * Default is $roster->config['locale']
 * @param bool $inline_caption | (optional)
 * Default is true
 * @return string | Formatted tooltip
 */
function colorTooltip( $tooltip, $caption_color='', $locale='', $inline_caption=1 )
{
	global $roster;

	// Use main locale if one is not specified
	if( $locale == '' )
	{
		$locale = $roster->config['locale'];
	}

	// Detect caption mode and display accordingly
	if( $inline_caption )
	{
		$first_line = true;
	}
	else
	{
		$first_line = false;
	}

	// Initialize tooltip_out
	$tooltip_out = array();

	// Color parsing time!
	$tooltip = str_replace("\n\n", "\n", $tooltip);
	$tooltip = str_replace('<br>',"\n",$tooltip);
	$tooltip = str_replace('<br />',"\n",$tooltip);
	foreach (explode("\n", $tooltip) as $line )
	{
		$color = '';

		if( !empty($line) )
		{
			$line = preg_replace('/\|c[a-f0-9]{2}([a-f0-9]{6})(.+?)\|r/i','<span style="color:#$1;">$2</span>',$line);

			// Do this on the first line
			// This is performed when $caption_color is set
			if( $first_line )
			{
				if( $caption_color == '' )
				{
					$caption_color = 'ffffff';
				}

				if( strlen($caption_color) > 6 )
				{
					$color = substr( $caption_color, 2, 6 ) . ';font-size:12px;font-weight:bold';
				}
				else
				{
					$color = $caption_color . ';font-size:12px;font-weight:bold';
				}

				$first_line = false;
			}
			else
			{
				if( preg_match( "/\b" . $roster->locale->wordings[$locale]['tooltip_use'] . "\b/i", $line) )
				{
					$color = '00ff00';
				}
				elseif( preg_match( "/\b" . $roster->locale->wordings[$locale]['tooltip_requires'] . "\b/i", $line) )
				{
					$color = 'ff0000';
				}
				elseif( preg_match( "/\b" . $roster->locale->wordings[$locale]['tooltip_reinforced'] . "\b/i", $line) )
				{
					$color = '00ff00';
				}
				elseif( preg_match( "/\b" . $roster->locale->wordings[$locale]['tooltip_equip'] . "\b/i", $line) )
				{
					$color = '00ff00';
				}
				elseif( preg_match( "/\b" . $roster->locale->wordings[$locale]['tooltip_chance'] . "\b/i", $line) )
				{
					$color = '00ff00';
				}
				elseif( preg_match( "/\b" . $roster->locale->wordings[$locale]['tooltip_enchant'] . "\b/i", $line) )
				{
					$color = '00ff00';
				}
				elseif( preg_match( "/\b" . $roster->locale->wordings[$locale]['tooltip_random_enchant'] . "\b/i", $line) )
				{
					$line = htmlspecialchars($line);
					$color = '00ff00';
				}
				elseif( preg_match( "/\b" . $roster->locale->wordings[$locale]['tooltip_accountbound'] . "\b/i", $line) )
				{
					$color = 'e5cc80';
				}
				elseif( preg_match( "/\b" . $roster->locale->wordings[$locale]['tooltip_soulbound'] . "\b/i", $line) )
				{
					$color = '00bbff';
				}
				elseif( preg_match( "/\b" . $roster->locale->wordings[$locale]['tooltip_set'] . "\b/i", $line) )
				{
					$color = '00ff00';
				}
				elseif(preg_match( "/" . $roster->locale->wordings[$locale]['tooltip_rank'] . "/i", $line) )
				{
					$color = '00ff00;font-weight:bold';
				}
				elseif(preg_match( "/\b" . $roster->locale->wordings[$locale]['tooltip_next_rank'] . "\b/i", $line) )
				{
					$color = 'ffffff;font-weight:bold';
				}
				elseif( preg_match('/\([a-f0-9]\).' . $roster->locale->wordings[$locale]['tooltip_set'] . '/i',$line) )
				{
					$color = '666666';
				}
				elseif( preg_match('/"/',$line) )
				{
					$color = 'ffd517';
				}
				elseif( preg_match( "/\b" . $roster->locale->wordings[$locale]['tooltip_garbage'] . "\b/i", $line) )
				{
					$line = '';
				}
				elseif( preg_match($roster->locale->wordings[$locale]['tooltip_preg_emptysocket'], $line, $matches) )
				{
					$line = '<img src="' . $roster->config['interface_url'] . 'Interface/ItemSocketingFrame/ui-emptysocket-'
						  . $roster->locale->wordings[$locale]['socket_colors_to_en'][strtolower($matches[1])] . '.' . $roster->config['img_suffix'] . '" />&nbsp;&nbsp;' . $matches[0];
				}
				elseif( preg_match($roster->locale->wordings[$locale]['tooltip_preg_classes'], $line, $matches) )
				{
					$classes = explode(' , ', $matches[2]);
					$count = count($classes);
					$class_text = $matches[1];

					$line = $class_text . '&nbsp;';
					$i = 0;
					foreach( $classes as $class )
					{
						$i++;
						$line .= '<span style="color:#' . $roster->locale->act['class_colorArray'][trim($class)] . ';">' . $class . '</span>';
						if( $count > $i )
						{
							$line .= ', ';
						}
					}
				}
			}

			// Convert tabs to a formated table
			if( strpos($line,"\t") )
			{
				$line = explode("\t",$line);
				if( !empty($color) )
				{
					$line = '<div style="width:100%;color:#' . $color . ';"><span style="float:right;">' . $line[1] . '</span>' . $line[0] . '</div>';
				}
				else
				{
					$line = '<div style="width:100%;"><span style="float:right;">' . $line[1] . '</span>' . $line[0] . '</div>';
				}
				$tooltip_out[] = $line;
			}
			elseif( !empty($color) )
			{
				$tooltip_out[] = '<span style="color:#' . $color . ';">' . $line . '</span>';
			}
			else
			{
				$tooltip_out[] = $line;
			}
		}
		else
		{
			$tooltip_out[] = '';
		}
	}
	return implode('<br />', $tooltip_out);
}

/**
 * Cleans up the tooltip and parses an inline_caption if needed
 * Use only with makeOverlib
 *
 * @param string $tooltip | Tooltip as a string (delimited by "\n" character)
 * @param string $caption_color | (optional) Color for the caption
 * Default is 'ffffff' - white
 * @param bool $inline_caption | (optional)
 * Default is true
 * @return string | Formatted tooltip
 */
function cleanTooltip( $tooltip , $caption_color='' , $inline_caption=1 )
{
	// Detect caption mode and display accordingly
	if( $inline_caption )
	{
		$first_line = true;
	}
	else
	{
		$first_line = false;
	}


	// Initialize tooltip_out
	$tooltip_out = array();

	// Parsing time!
	$tooltip = str_replace('<br>',"\n",$tooltip);
	$tooltip = str_replace('<br />',"\n",$tooltip);
	foreach( explode("\n", $tooltip) as $line )
	{
		$color = '';

		if( !empty($line) )
		{
			$line = preg_replace('|\\>|','&#8250;', $line );
			$line = preg_replace('|\\<|','&#8249;', $line );
			$line = preg_replace('|\|c[a-f0-9]{2}([a-f0-9]{6})(.+?)\|r|','<span style="color:#$1;">$2</span>',$line);

			// Do this on the first line
			// This is performed when $caption_color is set
			if( $first_line )
			{
				if( $caption_color == '' )
				{
					$caption_color = 'ffffff';
				}

				if( strlen($caption_color) > 6 )
				{
					$color = substr( $caption_color, 2, 6 ) . ';font-size:11px;font-weight:bold';
				}
				else
				{
					$color = $caption_color . ';font-size:11px;font-weight:bold';
				}

				$first_line = false;
			}

			// Convert tabs to a formated table
			if( strpos($line,"\t") )
			{
				$line = explode("\t",$line);
				if( !empty($color) )
				{
					$line = '<div style="width:100%;color:#' . $color . ';"><span style="float:right;">' . $line[1] . '</span>' . $line[0] . '</div>';
				}
				else
				{
					$line = '<div style="width:100%;"><span style="float:right;">' . $line[1] . '</span>' . $line[0] . '</div>';
				}
				$tooltip_out[] = $line;
			}
			elseif( !empty($color) )
			{
				$tooltip_out[] = '<span style="color:#' . $color . ';">' . $line . '</span>';
			}
			else
			{
				$tooltip_out[] = $line;
			}
		}
		else
		{
			$tooltip_out[] = '';
		}
	}

	return implode('<br />', $tooltip_out);
}


/**
 * Easy all in one function to make overlib tooltips
 * Creates a string for insertion into any html tag that has "onmouseover" and "onmouseout" events
 *
 * @param string $tooltip | Tooltip as a string (delimited by "\n" character)
 * @param string $caption | (optional) Text to set as a true OverLib caption
 * @param string $caption_color | (optional) Color for the caption
 * Default is 'ffffff' - white
 * @param bool $mode| (optional) Options 0=colorize,1=clean,2=pass through
 * Default 0 (colorize)
 * @param string $locale | Locale so color parser can work correctly
 * Only needed when $colorize is true
 * Default is $roster->config['locale']
 * @param string $extra_parameters | (optional) Extra OverLib parameters you wish to pass
 * @param string $item_id
 * @return unknown
 */
function makeOverlib( $tooltip , $caption='' , $caption_color='' , $mode=0 , $locale='' , $extra_parameters='', $type='text',$member_id = null )
{
	global $roster, $tooltips;

	$tooltip = stripslashes($tooltip);

	if ($type == 'text')
	{
		// Use main locale if one is not specified
		if( $locale == '' )
		{
			$locale = $roster->config['locale'];
		}
		// Detect caption text and display accordingly
		$caption_mode = 1;
		if( $caption_color != '' )
		{
			if( strlen($caption_color) > 6 )
			{
				$caption_color = substr( $caption_color, 2 );
			}
		}

		if( $caption != '' )
		{
			if( $caption_color != '' )
			{
				$caption = '<span style="color:#' . $caption_color . ';">' . $caption . '</span>';
			}

			$caption = addslashes($caption).'<br>';

			$caption_mode = 0;
		}

		switch ($mode)
		{
			case 0:
				$tooltip = colorTooltip($tooltip,$caption_color,$locale,$caption_mode);
				break;

			case 1:
				$tooltip = cleanTooltip($tooltip,$caption_color,$caption_mode);
				break;

			case 2:
				break;

			default:
				$tooltip = colorTooltip($tooltip,$caption_color,$locale,$caption_mode);
				break;
		}
		$t = '';
		if ( isset($tooltip) )
		{
			$t .= 'data-tooltip="text-' . base64_encode( $tooltip ) . '"';
		}
		if ( isset($caption) && !empty($caption) )
		{
			$t .= ' data-caption="'.base64_encode($caption).'"';
		}
		return $t;
	}
	else if ($type == 'item')
	{
		return 'data-tooltip="item-'.$tooltip.(isset($member_id) ? '|'.$member_id : '').'"';
	}
	else if ($type == 'talent')
	{
		return 'data-tooltip="talent-'.$tooltip.'"';
	}
	else
	{
		return null;
	}
	
	//return 'onmouseover="return overlib(overlib_' . $num_of_tips . $caption . $extra_parameters . ');" onmouseout="return nd();"';
}

/**
 * Recursively escape $array
 *
 * @param array $array
 *	The array to escape
 * @return array
 *	The same array, escaped
 */
function escape_array( $array )
{
	foreach ($array as $key=>$value)
	{
		if( is_array($value) )
		{
			$array[$key] = escape_array($value);
		}
		else
		{
			$array[$key] = addslashes($value);
		}
	}

	return $array;
}

/**
 * Recursively stripslash $array
 *
 * @param array $array
 *	The array to escape
 * @return array
 *	The same array, escaped
 */
function stripslash_array( $array )
{
	foreach ($array as $key=>$value)
	{
		if( is_array($value) )
		{
			$array[$key] = stripslash_array($value);
		}
		else
		{
			$array[$key] = stripslashes($value);
		}
	}

	return $array;
}

/**
 * Converts a datetime field into a readable date
 *
 * @param string $datetime datetime field data in DB
 * @param string $offset Offset in hours to calcuate time returned
 * @return string formatted date string
 */
function readbleDate( $datetime , $offset=null )
{
	global $roster;

	$offset = ( is_null($offset) ? $roster->config['localtimeoffset'] : $offset );

	list($year,$month,$day,$hour,$minute,$second) = sscanf($datetime,"%d-%d-%d %d:%d:%d");
	$localtime = mktime($hour+$offset ,$minute, $second, $month, $day, $year, -1);

	return date($roster->locale->act['phptimeformat'], $localtime);
}

/**
 * Gets a file's extention passed as a string
 *
 * @param string $filename
 * @return string
 */
function get_file_ext( $filename )
{
	return strtolower(ltrim(strrchr($filename,'.'),'.'));
}

/**
 * Converts seconds to a string delimited by time values
 * Will show w,d,h,m,s
 *
 * @param string $seconds
 * @return string
 */
function seconds_to_time( $seconds )
{
	while( $seconds >= 60 )
	{
		if( $seconds >= 86400 )
		{
			$days = floor($seconds / 86400);
			$seconds -= ($days * 86400);
		}
		elseif( $seconds >= 3600 )
		{
			$hours = floor($seconds / 3600);
			$seconds -= ($hours * 3600);
		}
		elseif( $seconds >= 60 )
		{
			$minutes = floor($seconds / 60);
			$seconds -= ($minutes * 60);
		}
	}

	// convert variables into sentence structure components
	$days = ( isset($days) ? $days . 'd, ' : '' );
	$hours = ( isset($hours) ? $hours . 'h, ' : '' );
	$minutes = ( isset($minutes) ? $minutes . 'm, ' : '' );
	$seconds = ( isset($seconds) ? $seconds . 's' : '' );

	return array('days' => $days, 'hours' => $hours, 'minutes' => $minutes, 'seconds' => $seconds);
}

/**
 * Sets up addon data for use in the addon framework
 *
 * @param string $addonname | The name of the addon
 * @return array $addon  | The addon's database record
 */
function getaddon( $addonname )
{
	global $roster;

	if ( !isset($roster->addon_data[$addonname]) )
	{
		//roster_die(sprintf($roster->locale->act['addon_not_installed'],$addonname),$roster->locale->act['addon_error']);
		return false;
	}

	$addon = $roster->addon_data[$addonname];
	// Get the addon's location
	$addon['dir'] = ROSTER_ADDONS . $addon['basename'] . DIR_SEP;

	// Get the addons url
	$addon['url'] = 'addons/' . $addon['basename'] . '/';
	$addon['url_full'] = ROSTER_URL . $addon['url'];
	$addon['url_path'] = ROSTER_PATH . $addon['url'];

	// Get addons url to images directory
	$addon['image_url'] = $addon['url_full'] . 'images/';
	$addon['image_path'] = $addon['url_path'] . 'images/';

	// Get the addon's global css style
	$addon['css_file'] = $addon['dir'] . 'style.css';

	if( file_exists($addon['css_file']) )
	{
		$addon['css_url'] = $addon['url'] . 'style.css';
	}
	else
	{
		$addon['css_file'] = '';
		$addon['css_url'] = '';
	}

	/**
	 * Template paths and urls
	 */

	// Get the addon's template path
	$addon['tpl_dir'] = ROSTER_TPLDIR . $roster->config['theme'] . DIR_SEP . $addon['basename'] . DIR_SEP;

	if( !file_exists($addon['tpl_dir']) )
	{
		$addon['tpl_dir'] = ROSTER_TPLDIR . 'default' . DIR_SEP . $addon['basename'] . DIR_SEP;
		$addon['tpl_url'] = 'templates/default/';
		$addon['tpl_url_full'] = ROSTER_URL . $addon['tpl_url'];
		$addon['tpl_url_path'] = ROSTER_PATH . $addon['tpl_url'];

		if( !file_exists($addon['tpl_dir']) )
		{
			$addon['tpl_dir'] = $addon['dir'] . 'templates' . DIR_SEP;
			$addon['tpl_url'] = $addon['url'] . 'templates/';
			$addon['tpl_url_full'] = $addon['url_full'] . 'templates/';
			$addon['tpl_url_path'] = $addon['url_path'] . 'templates/';

			if( !file_exists($addon['tpl_dir']) )
			{
				$addon['tpl_dir'] = '';
				$addon['tpl_url'] = '';
				$addon['tpl_url_full'] = '';
				$addon['tpl_url_path'] = '';
			}
		}
	}
	else
	{
		$addon['tpl_url'] = 'templates/' . $roster->config['theme'] . '/' . $addon['basename'] . '/';
		$addon['tpl_url_full'] = ROSTER_URL . $addon['tpl_url'];
		$addon['tpl_url_path'] = ROSTER_PATH . $addon['tpl_url'];
	}

	// Get addons url to template images directory
	$addon['tpl_image_url'] = $addon['tpl_url_full'] . 'images/';
	$addon['tpl_image_path'] = $addon['tpl_url_path'] . 'images/';

	// Get the addon's template based css style
	$addon['tpl_css_file'] = $addon['tpl_dir'] . 'style.css';

	if( file_exists($addon['tpl_css_file']) )
	{
		$addon['tpl_css_url'] = $addon['tpl_url'] . 'style.css';
	}
	else
	{
		$addon['tpl_css_file'] = '';
		$addon['tpl_css_url'] = '';
	}

	/**
	 * End Template paths and urls
	 */

	// Get the addon's inc dir
	$addon['inc_dir'] = $addon['dir'] . 'inc' . DIR_SEP;

	// Get the addon's conf file
	$addon['conf_file'] = $addon['inc_dir'] . 'conf.php';

	// Get the addon's search file
	$addon['search_file'] = $addon['inc_dir'] . 'search.inc.php';
	$addon['search_class'] = $addon['basename'] . 'Search';

	// Get the addon's locale dir
	$addon['locale_dir'] = $addon['dir'] . 'locale' . DIR_SEP;

	// Get the addon's admin dir
	$addon['admin_dir'] = $addon['dir'] . 'admin' . DIR_SEP;

	// Get the addon's admin dir
	$addon['ucp_dir'] = $addon['dir'] . 'ucp' . DIR_SEP;
	
	// Get the addon's trigger file
	$addon['trigger_file'] = $addon['inc_dir'] . 'update_hook.php';

	// Get the addon's ajax functions file
	$addon['ajax_file'] = $addon['inc_dir'] . 'ajax.php';

	// Get config values for the default profile and insert them into the array
	$addon['config'] = array();

	$query = "SELECT `config_name`, `config_value` FROM `" . $roster->db->table('addon_config') . "` WHERE `addon_id` = '" . $addon['addon_id'] . "' ORDER BY `id` ASC;";

	$result = $roster->db->query($query);

	if ( !$result )
	{
		die_quietly($roster->db->error(),$roster->locale->act['addon_error'],__FILE__,__LINE__, $query );
	}

	if( $roster->db->num_rows($result) > 0 )
	{
		while( $row = $roster->db->fetch($result,SQL_ASSOC) )
		{
			$addon['config'][$row['config_name']] = $row['config_value'];
		}
		$roster->db->free_result($result);
	}

	return $addon;
}


/**
 * Sets up plugin data for use in the plugin framework
 *
 * @param string $pluginname | The name of the plugin
 * @return array $plugin  | The plugin's database record
 */
function getplugin( $pluginname )
{
	global $roster;

	if ( !isset($roster->plugin_data[$pluginname]) )
	{
		roster_die(sprintf($roster->locale->act['plugin_not_installed'],$pluginname),$roster->locale->act['plugin_error']);
	}

	$plugin = $roster->plugin_data[$pluginname];

	// Get the plugin's location
	$plugin['dir'] = ROSTER_PLUGINS . $plugin['basename'] . DIR_SEP;

	// Get the plugins url
	$plugin['url'] = 'plugins/' . $plugin['basename'] . '/';
	$plugin['url_full'] = ROSTER_URL . $plugin['url'];
	$plugin['url_path'] = ROSTER_PATH . $plugin['url'];

	// Get plugins url to images directory
	$plugin['image_url'] = $plugin['url_full'] . 'images/';
	$plugin['image_path'] = $plugin['url_path'] . 'images/';

	// Get the plugin's global css style
	$plugin['css_file'] = $plugin['dir'] . 'style.css';

	if( file_exists($plugin['css_file']) )
	{
		$plugin['css_url'] = $plugin['url'] . 'style.css';
	}
	else
	{
		$plugin['css_file'] = '';
		$plugin['css_url'] = '';
	}

	/**
	 * Template paths and urls
	 */

	// Get the plugin's template path
	$plugin['tpl_dir'] = ROSTER_TPLDIR . $roster->config['theme'] . DIR_SEP . $plugin['basename'] . DIR_SEP;

	if( !file_exists($plugin['tpl_dir']) )
	{
		$plugin['tpl_dir'] = ROSTER_TPLDIR . 'default' . DIR_SEP . $plugin['basename'] . DIR_SEP;
		$plugin['tpl_url'] = 'templates/default/';
		$plugin['tpl_url_full'] = ROSTER_URL . $plugin['tpl_url'];
		$plugin['tpl_url_path'] = ROSTER_PATH . $plugin['tpl_url'];

		if( !file_exists($plugin['tpl_dir']) )
		{
			$plugin['tpl_dir'] = $plugin['dir'] . 'templates' . DIR_SEP;
			$plugin['tpl_url'] = $plugin['url'] . 'templates/';
			$plugin['tpl_url_full'] = $plugin['url_full'] . 'templates/';
			$plugin['tpl_url_path'] = $plugin['url_path'] . 'templates/';

			if( !file_exists($plugin['tpl_dir']) )
			{
				$plugin['tpl_dir'] = '';
				$plugin['tpl_url'] = '';
				$plugin['tpl_url_full'] = '';
				$plugin['tpl_url_path'] = '';
			}
		}
	}
	else
	{
		$plugin['tpl_url'] = 'templates/' . $roster->config['theme'] . '/' . $plugin['basename'] . '/';
		$plugin['tpl_url_full'] = ROSTER_URL . $plugin['tpl_url'];
		$plugin['tpl_url_path'] = ROSTER_PATH . $plugin['tpl_url'];
	}

	// Get plugins url to template images directory
	$plugin['tpl_image_url'] = $plugin['tpl_url_full'] . 'images/';
	$plugin['tpl_image_path'] = $plugin['tpl_url_path'] . 'images/';

	// Get the plugin's template based css style
	$plugin['tpl_css_file'] = $plugin['tpl_dir'] . 'style.css';

	if( file_exists($plugin['tpl_css_file']) )
	{
		$plugin['tpl_css_url'] = $plugin['tpl_url'] . 'style.css';
	}
	else
	{
		$plugin['tpl_css_file'] = '';
		$plugin['tpl_css_url'] = '';
	}

	/**
	 * End Template paths and urls
	 */

	// Get the plugin's inc dir
	$plugin['inc_dir'] = $plugin['dir'] . 'inc' . DIR_SEP;

	// Get the plugin's conf file
	$plugin['conf_file'] = $plugin['inc_dir'] . 'conf.php';

	// Get the plugin's search file
	$plugin['search_file'] = $plugin['inc_dir'] . 'search.inc.php';
	$plugin['search_class'] = $plugin['basename'] . 'Search';

	// Get the plugin's locale dir
	$plugin['locale_dir'] = $plugin['dir'] . 'locale' . DIR_SEP;

	// Get the plugin's admin dir
	$plugin['admin_dir'] = $plugin['dir'] . 'admin' . DIR_SEP;

	// Get the plugin's trigger file
	$plugin['trigger_file'] = $plugin['inc_dir'] . 'update_hook.php';

	// Get the plugin's ajax functions file
	$plugin['ajax_file'] = $plugin['inc_dir'] . 'ajax.php';

	// Get config values for the default profile and insert them into the array
	$plugin['config'] = '';

	$query = "SELECT `config_name`, `config_value` FROM `" . $roster->db->table('plugin_config') . "` WHERE `addon_id` = '" . $plugin['addon_id'] . "' ORDER BY `id` ASC;";

	$result = $roster->db->query($query);

	if ( !$result )
	{
		die_quietly($roster->db->error(),$roster->locale->act['plugin_error'],__FILE__,__LINE__, $query );
	}

	if( $roster->db->num_rows($result) > 0 )
	{
		while( $row = $roster->db->fetch($result,SQL_ASSOC) )
		{
			$plugin['config'][$row['config_name']] = $row['config_value'];
		}
		$roster->db->free_result($result);
	}

	return $plugin;
}


/**
 * Check to see if an addon is active or not
 *
 * @param string $name | Addon basename
 * @return bool
 */
function active_addon( $name )
{
	global $roster;

	if( !isset($roster->addon_data[$name]) )
	{
		return false;
	}
	else
	{
		return (bool)$roster->addon_data[$name]['active'];
	}
}

/**
 * Handles retrieving the contents of a URL trying multiple methods
 * Current methods are curl, file_get_contents, fsockopen and will try each in that order
 *
 * @param string $url	| URL to retrieve
 * @param int $timeout	| Timeout for curl, socket connection timeout for fsock
 * @param  string $user_agent	| Useragent to use for connection
 * @return mixed		| False on error, contents on success
 */
function urlgrabber( $url , $timeout=5 , $user_agent=false, $loopcount=0 )
{
	global $roster;

	$pUrl = parse_url($url);
	$cache_tag = $pUrl['host'] . '_cookie';

	$loopcount++;
	$contents = '';

	if( $loopcount > 2 )
	{
		trigger_error("UrlGrabber Error: To many loops. Unable to grab URL ($url)", E_USER_WARNING);
		return $contents;
	}

	if( function_exists('curl_init') )
	{
//		trigger_error('UrlGrabber Info [CURL]: Activated', E_USER_WARNING);
		$ch = curl_init($url);

		$httpHeader = array( 'Accept-Language: ' . substr($roster->config['locale'], 0, 2) );
		if( $roster->cache->check($cache_tag) )
		{
			$httpHeader[] = 'Cookie: ' . $roster->cache->get($cache_tag);
		}
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
		if( $user_agent )
		{
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
		}
		$contents = curl_exec($ch);

		// If there were errors
		if( curl_errno($ch) )
		{
			trigger_error('UrlGrabber Error [CURL]: ' . curl_error($ch), E_USER_WARNING);
			return false;
		}

		if( preg_match('/\r/', $contents, $tmp) )
		{
			list($resHeader, $data) = explode("\r\n\r\n", $contents, 2);
		}
		else
		{
			list($resHeader, $data) = explode("\n\n", $contents, 2);
		}
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$tmp;
		if( preg_match('/(?:Set-Cookie: (.+))/', $resHeader, $tmp) )
		{
			$roster->cache->put($tmp[1], $cache_tag);
		}

		if( $http_code == 301 || $http_code == 302 )
		{
			$matches = array();
			preg_match('/Location:(.*?)\n/', $resHeader, $matches);
			$redirect = trim(array_pop($matches));
			if( !$redirect )
			{
				//couldn't process the url to redirect to
				return $data;
			}

			return urlgrabber( $redirect, $timeout, $user_agent, $loopcount );
		}
		else
		{
			return $data;
		}
	}
	elseif( preg_match('/\bhttps?:\/\/([-A-Z0-9.]+):?(\d+)?(\/[-A-Z0-9+&@#\/%=~_|!:,.;]*)?(\?[-A-Z0-9+&@#\/%=~_|!:,.;]*)?/i', $url, $matches) )
	{
//		trigger_error('UrlGrabber Info [fsock]: Activated', E_USER_WARNING);
		// 0 = $url, 1 = host, 2 = port or null, 3 = page requested, 4 = pararms
		$host = $matches[1];
		$port = (($matches[2] == '') ? 80 : $matches[2]);
		$page = $matches[3];
		$page_params = ( isset($matches[4]) ? $matches[4] : '' );

		$file = fsockopen($host, $port, $errno, $errstr, $timeout);
		if( !$file )
		{
			trigger_error("UrlGrabber Error [fsock]: $errstr ($errno)", E_USER_WARNING);
			return false;
		}
		else
		{
			$header = "GET $page$page_params HTTP/1.0\r\n"
					. "Host: $host\r\n"
					. "User-Agent: $user_agent\r\n"
					. "Accept-Language: " . substr($roster->config['locale'], 0, 2) . "\r\n"
					. "Connection: Close\r\n";
			if( $roster->cache->check($cache_tag) )
			{
				$header .= "Cookie: " . $roster->cache->get($cache_tag) . "\r\n";
			}
			$header .= "\r\n";
			fwrite($file, $header);
			stream_set_blocking($file, true);
			stream_set_timeout($file, $timeout);

			$info = stream_get_meta_data($file);
			$inHeader = true;
			$redirect = false;
			$resHeader = '';
			$tmp = '';
			while( (!feof($file)) && (!$info['timed_out']) )
			{
				$chunk = fgets($file, 256);
				$info = stream_get_meta_data($file);
				if( $inHeader )
				{
					if( $chunk == "\r\n" || $chunk == "\n" )
					{
						$inHeader = false;
					}
					else
					{
						$resHeader .= $chunk;
						if( preg_match('/^(?:Location:\s)(.+)/', $chunk, $tmp) )
						{
							$redirect = $tmp[1];
						}
					}
					continue;
				}
				$contents .= $chunk;
			}
			fclose($file);
			if( $info['timed_out'] )
			{
				trigger_error("UrlGrabber Error [fsock]: Timed out", E_USER_WARNING);
			}
			if( preg_match('/(?:Set-Cookie: )(.+)/', $resHeader, $tmp) )
			{
				$roster->cache->put($tmp[1], $cache_tag);
			}
			if( $redirect != false )
			{
				return urlgrabber( $redirect, $timeout, $user_agent, $loopcount );
			}
			else
			{
				return $contents;
			}
		}
	}
	elseif( $contents = file_get_contents($url) )
	{
//		trigger_error('UrlGrabber Info [file_get_contents]: Activated', E_USER_WARNING);
		return $contents;
	}
	else
	{
		trigger_error("UrlGrabber Error: Unable to grab URL ($url)", E_USER_WARNING);
		return false;
	}
} //-END function urlgrabber()

/**
 * Stupid function to create an REQUEST_URI for IIS 5 servers
 *
 * @return string
 */
function request_uri( )
{
	if( preg_match('/\bIIS\b/i', $_SERVER['SERVER_SOFTWARE']) && isset($_SERVER['SCRIPT_NAME']) )
	{
		$REQUEST_URI = $_SERVER['SCRIPT_NAME'];
		if( isset($_SERVER['QUERY_STRING']) )
		{
			$REQUEST_URI .= '?' . $_SERVER['QUERY_STRING'];
		}
	}
	else
	{
		$REQUEST_URI = $_SERVER['REQUEST_URI'];
	}
	# firefox encodes url by default but others don't
	$REQUEST_URI = urldecode($REQUEST_URI);
	# encode the url " %22 and <> %3C%3E
	$REQUEST_URI = str_replace('"', '%22', $REQUEST_URI);
	$REQUEST_URI = preg_replace('#([\x3C\x3E])#', '"%".bin2hex(\'\\1\')', $REQUEST_URI);
	$REQUEST_URI = substr($REQUEST_URI, 0, strlen($REQUEST_URI)-strlen(stristr($REQUEST_URI, '&CMSSESSID')));

	return $REQUEST_URI;
}


/**
 * Attempts to write a file to the file system
 *
 * @param string $filename
 * @param string $content
 * @param string $mode
 * @return bool
 */
function file_writer( $filename , &$content , $mode='wb' )
{
	if(!$fp = fopen($filename, $mode))
	{
		trigger_error("Cannot open file ($filename)", E_USER_WARNING);
		return false;
	}
	flock($fp, LOCK_EX);
	$bytes_written = fwrite($fp, $content);
	flock($fp, LOCK_UN);
	fclose($fp);
	if($bytes_written === FALSE)
	{
		trigger_error("Couldn't write to file ($filename)", E_USER_WARNING);
		return false;
	}
	if( !defined('PHP_AS_NOBODY') )
	{
		php_as_nobody($filename);
	}
	chmod($filename, (PHP_AS_NOBODY ? 0666 : 0644));
	return true;
}

function php_as_nobody( $file )
{
	if( !defined('PHP_AS_NOBODY') )
	{
		define('PHP_AS_NOBODY', (ROSTER_PROCESS_OWNER == 'nobody' || getmyuid() != fileowner($file)));
	}
}

/**
 * Wrapper for debugging function dumps arrays/object formatted
 *
 * @param array $arr
 * @param string $prefix
 * @return string
 */
function aprint( $arr , $prefix='' , $return=false )
{
	if( $return )
	{
		return APrint::dump($arr);
	}
	else
	{
		echo APrint::dump($arr);
	}
}

function format_microtime( )
{
	list($usec, $sec) = explode(' ', microtime());
	return ($usec + $sec);
}

/**
 * A better array_merge()
 * Merges multi-dimensional arrays
 *
 * @param array $skel
 * @param array $arr
 * @return array
 */
function array_overlay( $skel , &$arr )
{
	foreach ($skel as $key => $val)
	{
		if( !isset($arr[$key]) )
		{
			$arr[$key] = $val;
		}
		elseif( is_array($val) )
		{
			$arr[$key] = array_overlay($val, $arr[$key]);
		}
		else
		{
			// UnComment if you want to know if you are overwritting a variable
			//trigger_error('Key already set: ' . $key . '->' . $arr[$key] . '<br />&nbsp;&nbsp;New value tried: ' . $skel[$key]);
		}
	}

	return $arr;
}

/**
 * Checks an addon download id on the wowroster.net rss feed
 * And informs if there is an update
 *
 * @param string $name | name of the download
 * @param string $url | url
 */
function updateCheck( $addon )
{
	global $roster;

	if( $roster->config['check_updates'] && isset($addon['wrnet_id']) && !empty($addon['wrnet_id']) )
	{
		$cache = unserialize($addon['versioncache']);

		if( $addon['versioncache'] == '' )
		{
			$cache['timestamp'] = 0;
			$cache['ver_latest'] = '';
			$cache['ver_info'] = '';
			$cache['ver_link'] = '';
			$cache['ver_date'] = '';
		}

		if( ($cache['timestamp'] + (60 * 60 * $roster->config['check_updates'])) <= time() )
		{
			$cache['timestamp'] = time();

			$content = urlgrabber(sprintf(ROSTER_ADDONUPDATEURL,$addon['wrnet_id']));

			if( preg_match('#<version>(.+)</version>#i',$content,$info) )
			{
				$cache['ver_latest'] = $info[1];
			}

			if( preg_match('#<info>(.+)</info>#i',$content,$info) )
			{
				$cache['ver_info'] = $info[1];
			}

			if( preg_match_all('#<link>(.+)</link>#i',$content,$info) )
			{
				$cache['ver_link'] = $info[1][2];
			}

			if( preg_match('#<updated>(.+)</updated>#i',$content,$info) )
			{
				$cache['ver_date'] = $info[1];
			}

			$roster->db->query ( "UPDATE `" . $roster->db->table('addon') . "` SET `versioncache` = '" . serialize($cache) . "' WHERE `addon_id` = '" . $addon['addon_id'] . "' LIMIT 1;");
		}

		if( version_compare($cache['ver_latest'],$addon['version'],'>') )
		{
			// Save current locale array
			// Since we add all locales for localization, we save the current locale array
			// This is in case one addon has the same locale strings as another, and keeps them from overwritting one another
			$localetemp = $roster->locale->wordings;

			foreach( $roster->multilanguages as $lang )
			{
				$roster->locale->add_locale_file(ROSTER_ADDONS . $addon['basename'] . DIR_SEP . 'locale' . DIR_SEP . $lang . '.php',$lang);
			}

			$name = ( isset($roster->locale->act[$addon['fullname']]) ? $roster->locale->act[$addon['fullname']] : $addon['fullname'] );

			// Restore our locale array
			$roster->locale->wordings = $localetemp;
			unset($localetemp);

			$cache['ver_date'] = date($roster->locale->act['phptimeformat'], $cache['ver_date'] + (3600*$roster->config['localtimeoffset']));
			$roster->set_message(sprintf($roster->locale->act['new_version_available'], $name, $cache['ver_latest'], $cache['ver_date'], $cache['ver_link']), $roster->locale->act['update']
				. ': ' . $name . $cache['ver_info']
			);
		}
	}
}

/**
 * Dummy function. For when you need a callback that doesn't do anything.
 */
function dummy(){}


/**
 * A nifty Pagination function, sets template variables
 * Can only be used once on a page
 *
 * @param string $base_url
 * @param int $num_items
 * @param int $per_page
 * @param int $start_item
 * @param bool $add_prevnext
 * @return void
 */
//paginate
function paginate( $base_url , $num_items , $per_page , $start_item , $add_prevnext=true,$cols=false )
{
	$this->paginate2($base_url, $num_items, $per_page, $start_item, $add_prevnext_text = true,$cols=false);
}

function paginate2($base_url, $num_items, $per_page, $start_item, $add_prevnext_text = true,$cols=false)
{
	global $roster;

	// Make sure $per_page is a valid value
	$per_page = ($per_page <= 0) ? 1 : $per_page;

	$total_pages = ceil($num_items / $per_page);

	if ($total_pages == 1 || !$num_items)
	{
		return false;
	}

	$on_page = floor($start_item / $per_page) + 1;
	$url_delim = (strpos($base_url, '?') === false) ? '?' : ((strpos($base_url, '?') === strlen($base_url) - 1) ? '' : '&amp;');

	$page_string = ($on_page == 1) ? '<span class="pagi-selected">1</span>' : '<a href="' . makelink($base_url . '0','members') . '"><span class="pagi-active">1</span></a>';
	$roster->tpl->assign_block_vars('pagination_pages',array(
				'ACTIVE'	=> (($on_page == 1) ? true : false),
				'DISABLED'	=> false,
				'URL'		=> makelink($base_url . '0', 'members'),
				'PAGE'		=> '1',
				));

	if ($total_pages > 5)
	{
		$start_cnt = min(max(1, $on_page - 5), $total_pages - 4);
		$end_cnt = max(min($total_pages, $on_page + 5), 5);

		$page_string .= ($start_cnt > 1) ? '... ' : '';

		for ($i = $start_cnt + 1; $i < $end_cnt; $i++)
		{
			$page_string .= ($i == $on_page) ? '<span class="pagi-selected">' . $i . '</span>' : '<a href="' . makelink($base_url . (($i-1) * $per_page), 'members') . '"><span class="pagi-active">' . $i . '</span></a>';
			$roster->tpl->assign_block_vars('pagination_pages',array(
				'ACTIVE'	=> (($i == $on_page) ? true : false),
				'DISABLED'	=> false,
				'URL'		=> makelink($base_url . (($i-1) * $per_page), 'members'),
				'PAGE'		=> $i,
				));
		}

		$page_string .= ($end_cnt < $total_pages) ? '... ' : '';
		$roster->tpl->assign_block_vars('pagination_pages',array(
				'ACTIVE'	=> false,
				'DISABLED'	=> true,
				'URL'		=> '',
				'PAGE'		=> '...',
				));
				
		$page_string .= ($on_page == $total_pages) ? '<span class="pagi-selected">' . $total_pages . '</span>' : '<a href="' . makelink($base_url . (($total_pages - 1) * $per_page), 'members') . '"><span class="pagi-active">'.$total_pages.'</span></a>';
		$roster->tpl->assign_block_vars('pagination_pages',array(
				'ACTIVE'	=> (($on_page == $total_pages) ? true : false),
				'DISABLED'	=> false,
				'URL'		=> makelink($base_url . (($total_pages - 1) * $per_page), 'members'),
				'PAGE'		=> $total_pages,
				));
	}
	else
	{
		for ($i = 3; $i <= $total_pages; $i++)
		{
			$page_string .= ($i == $on_page) ? '<span class="pagi-selected">' . $i . '</span>' : '<a href="' . makelink($base_url . (($i-1) * $per_page), 'members') . '"><span class="pagi-active">' . $i . '</span></a>';
			$roster->tpl->assign_block_vars('pagination_pages',array(
				'ACTIVE'	=> (($i == $on_page) ? true : false),
				'DISABLED'	=> false,
				'URL'		=> makelink($base_url . (($i-1) * $per_page), 'members'),
				'PAGE'		=> $i,
				));
		}
	}

	$roster->tpl->assign_vars(array(
		'URL'             => $base_url,
		'BASE_URL'        => addslashes($base_url),
		'PER_PAGE'        => $per_page,
		'COLS'            => $cols,
		'B_PAGINATION'    => true,
		'PAGINATION_PREV' => (($add_prevnext_text && $on_page > 1) ? makelink($base_url . ($start_item - $per_page)) : false),
		'PAGINATION_NEXT' => (($add_prevnext_text && $on_page < $total_pages) ? makelink($base_url . ($start_item + $per_page)) : false),
		'TOTAL_PAGES'     => $total_pages,
		'CURRENT_PAGE'    => $on_page,
		'PAGE'            => $page_string,
	));

	//return $page_string;
}


/**
 * Makes the Realmstatus block
 *
 * @return the formatted realmstatus block
 */
function makeRealmStatus( )
{
	global $roster;

	$realmStatus = "\n";

	if( isset($roster->data['server']) )
	{
		$realmname = $roster->data['region'] . '-' . utf8_decode($roster->data['server']);
	}
	else
	{
		// Get the default selected guild from the upload rules
		$query =  "SELECT `name`, `server`, `region`"
				. " FROM `" . $roster->db->table('upload') . "`"
				. " WHERE `default` = '1' LIMIT 1;";

		$roster->db->query($query);

		if( $roster->db->num_rows() > 0 )
		{
			$data = $roster->db->fetch();

			$realmname = $data['region'] . '-' . utf8_decode($data['server']);
		}
		else
		{
			$realmname = '';
		}
	}

	if( !empty($realmname) )
	{
		if( $roster->config['rs_display'] == 'image' )
		{
			$realmStatus .= '<img alt="Realm Status" src="' . ROSTER_URL . 'realmstatus2.php?r=' . urlencode($realmname) . '" />' . "\n";
		}
		elseif( $roster->config['rs_display'] == 'text' && file_exists(ROSTER_BASE . 'realmstatus.php') )
		{
			//$_GET['r'] = urlencode($realmname);
			ob_start();
				include_once (ROSTER_BASE . 'realmstatus.php');
			$realmStatus .= ob_get_clean() . "\n";
		}
		else
		{
			$realmStatus .= '&nbsp;';
		}

	}
	else
	{
		$realmStatus .= '&nbsp;';
	}

	$realmStatus .= "\n";

	return $realmStatus;
}

/**
* Return unique id
* @param string $extra additional entropy
*/
function unique_id($extra = 'c')
{
	static $dss_seeded = false;
	global $config;

	$val = $config['rand_seed'] . microtime();
	$val = md5($val);

	return substr($val, 4, 16);
}

/*
	new admin area message gathering.
*/

function _getAdminMessages()
{
	global $roster, $addons;
	
	$msgc = 0;
	/*
		get addon messages
	*/
	//d($roster->addon_data);
	$addons = getAddonList();
	//d($addons);
	foreach( $addons as $addon )
	{
		if ($addon['install'] == '1')
		{
			$msgc++;
			if( !empty($addon['icon']) )
			{
				if( strpos($addon['icon'],'.') !== false )
				{
					$addon['icon'] = ROSTER_PATH . 'addons/' . $addon['basename'] . '/images/' . $addon['icon'];
				}
				else
				{
					$addon['icon'] = $roster->config['interface_url'] . 'Interface/Icons/' . $addon['icon'] . '.' . $roster->config['img_suffix'];
				}
			}
			else
			{
				$addon['icon'] = $roster->config['interface_url'] . 'Interface/Icons/inv_misc_questionmark.' . $roster->config['img_suffix'];
			}

			$roster->tpl->assign_block_vars('roster_cp', array(
				'ICON'        => $addon['icon'],
				'FULLNAME'    => $addon['fullname'],
				'BASENAME'    => $addon['basename'],
				'VERSION'     => $addon['version'],
				'OLD_VERSION' => ( isset($addon['oldversion']) ? $addon['oldversion'] : '' ),
				'INSTALL'     => $addon['install'],
				'L_TIP_UPGRADE' => ( isset($addon['active']) ? sprintf($roster->locale->act['installer_click_upgrade'],$addon['oldversion'],$addon['version']) : '' ),
				)
			);
		}
	}
}

function roster_404()
{
	global $roster;
	$roster->tpl->set_handle('r404','404.html');
	$roster->tpl->display('r404');
}

function roster_useronly()
{
	global $roster;
	$roster->tpl->set_handle('r404','404.html');
	$roster->tpl->display('r404');
}

function bbcode_nl2br($text)
{
	// custom BBCodes might contain carriage returns so they
	// are not converted into <br /> so now revert that
	$text = str_replace(array("\n", "\r"), array('<br />', "\n"), $text);
	return $text;
}

function getAddonList()
{
	global $roster, $installer;

	// Initialize output
	$addons = array();
	$output = array();

	if( $handle = @opendir(ROSTER_ADDONS) )
	{
		while( false !== ($file = readdir($handle)) )
		{
			if( $file != '.' && $file != '..' && $file != '.svn' && substr($file, strrpos($file, '.')+1) != 'txt')
			{
				$addons[] = $file;
			}
		}
	}

	usort($addons, 'strnatcasecmp');

	if( is_array($addons) )
	{
		foreach( $addons as $addon )
		{
			$installfile = ROSTER_ADDONS . $addon . DIR_SEP . 'inc' . DIR_SEP . 'install.def.php';
			$install_class = $addon . 'Install';

			if( file_exists($installfile) )
			{
				include_once($installfile);

				if( !class_exists($install_class) )
				{
					$installer->seterrors(sprintf($roster->locale->act['installer_no_class'],$addon));
					continue;
				}

				$addonstuff = new $install_class;

				if( array_key_exists($addon,$roster->addon_data) )
				{

					$output[$addon]['id'] = $roster->addon_data[$addon]['addon_id'];
					$output[$addon]['active'] = $roster->addon_data[$addon]['active'];
					$output[$addon]['access'] = $roster->addon_data[$addon]['access'];
					$output[$addon]['oldversion'] = $roster->addon_data[$addon]['version'];

					// -1 = overwrote newer version
					//  0 = same version
					//  1 = upgrade available
					$output[$addon]['install'] = version_compare($addonstuff->version,$roster->addon_data[$addon]['version']);
					if ($output[$addon]['install'] == 0 && $output[$addon]['active'] == 0)
					{
						$output[$addon]['install'] = 2;
					}
					
				}
				/*
				else if ($output[$addon]['install'] == 0 && $output[$addon]['active'] == 0)
				{
					$output[$addon]['install'] = 2;
				}*/
				else
				{
					$output[$addon]['install'] = 3;
				}

				// Save current locale array
				// Since we add all locales for localization, we save the current locale array
				// This is in case one addon has the same locale strings as another, and keeps them from overwritting one another
				$localetemp = $roster->locale->wordings;

				foreach( $roster->multilanguages as $lang )
				{
					$roster->locale->add_locale_file(ROSTER_ADDONS . $addon . DIR_SEP . 'locale' . DIR_SEP . $lang . '.php',$lang);
				}

				$output[$addon]['basename'] = $addon;
				$output[$addon]['fullname'] = ( isset($roster->locale->act[$addonstuff->fullname]) ? $roster->locale->act[$addonstuff->fullname] : $addonstuff->fullname );
				$output[$addon]['author'] = $addonstuff->credits[0]['name'];
				$output[$addon]['version'] = $addonstuff->version;
				$output[$addon]['icon'] = $addonstuff->icon;
				$output[$addon]['description'] = ( isset($roster->locale->act[$addonstuff->description]) ? $roster->locale->act[$addonstuff->description] : $addonstuff->description );
				$output[$addon]['requires'] = (isset($addonstuff->requires) ? $roster->locale->act['tooltip_reg_requires'].' '.$addonstuff->requires : '');

				unset($addonstuff);

				// Restore our locale array
				$roster->locale->wordings = $localetemp;
				unset($localetemp);
			}
		}
	}
	return $output;
}

function __debug__($v)
{
	global $roster;
	
	$roster->set_message( "Debug active", 'Debug Trigger', 'alert' );
	d($v);
}

/*
	new hook and filter setup
*/

// Initialize the filter globals.
require( dirname( __FILE__ ) . '/roster_hook.php' );

/** @var Roster_Hook[] $roster_filter */
global $roster_filter, $roster_actions, $roster_current_filter;

if ( $roster_filter ) {
	$roster_filter = Roster_Hook::build_preinitialized_hooks( $roster_filter );
} else {
	$roster_filter = array();
}

if ( ! isset( $roster_actions ) )
	$roster_actions = array();

if ( ! isset( $roster_current_filter ) )
	$roster_current_filter = array();

/**
 * Hook a function or method to a specific filter action.
 *
 * WordPress offers filter hooks to allow plugins to modify
 * various types of internal data at runtime.
 *
 * A plugin can modify data by binding a callback to a filter hook. When the filter
 * is later applied, each bound callback is run in order of priority, and given
 * the opportunity to modify a value by returning a new value.
 *
 * The following example shows how a callback function is bound to a filter hook.
 *
 * Note that `$example` is passed to the callback, (maybe) modified, then returned:
 *
 *     function example_callback( $example ) {
 *         // Maybe modify $example in some way.
 *         return $example;
 *     }
 *     add_filter( 'example_filter', 'example_callback' );
 *
 * Bound callbacks can accept from none to the total number of arguments passed as parameters
 * in the corresponding apply_filters() call.
 *
 * In other words, if an apply_filters() call passes four total arguments, callbacks bound to
 * it can accept none (the same as 1) of the arguments or up to four. The important part is that
 * the `$accepted_args` value must reflect the number of arguments the bound callback *actually*
 * opted to accept. If no arguments were accepted by the callback that is considered to be the
 * same as accepting 1 argument. For example:
 *
 *     // Filter call.
 *     $value = apply_filters( 'hook', $value, $arg2, $arg3 );
 *
 *     // Accepting zero/one arguments.
 *     function example_callback() {
 *         ...
 *         return 'some value';
 *     }
 *     add_filter( 'hook', 'example_callback' ); // Where $priority is default 10, $accepted_args is default 1.
 *
 *     // Accepting two arguments (three possible).
 *     function example_callback( $value, $arg2 ) {
 *         ...
 *         return $maybe_modified_value;
 *     }
 *     add_filter( 'hook', 'example_callback', 10, 2 ); // Where $priority is 10, $accepted_args is 2.
 *
 * *Note:* The function will return true whether or not the callback is valid.
 * It is up to you to take care. This is done for optimization purposes, so
 * everything is as quick as possible.
 *
 * @since 0.71
 *
 * @global array $roster_filter      A multidimensional array of all hooks and the callbacks hooked to them.
 *
 * @param string   $tag             The name of the filter to hook the $function_to_add callback to.
 * @param callable $function_to_add The callback to be run when the filter is applied.
 * @param int      $priority        Optional. Used to specify the order in which the functions
 *                                  associated with a particular action are executed. Default 10.
 *                                  Lower numbers correspond with earlier execution,
 *                                  and functions with the same priority are executed
 *                                  in the order in which they were added to the action.
 * @param int      $accepted_args   Optional. The number of arguments the function accepts. Default 1.
 * @return true
 */
function add_filter( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
	global $roster_filter;
	if ( ! isset( $roster_filter[ $tag ] ) ) {
		$roster_filter[ $tag ] = new Roster_Hook();
	}
	$roster_filter[ $tag ]->add_filter( $tag, $function_to_add, $priority, $accepted_args );
	return true;
}

/**
 * Check if any filter has been registered for a hook.
 *
 * @since 2.5.0
 *
 * @global array $roster_filter Stores all of the filters.
 *
 * @param string        $tag               The name of the filter hook.
 * @param callable|bool $function_to_check Optional. The callback to check for. Default false.
 * @return false|int If $function_to_check is omitted, returns boolean for whether the hook has
 *                   anything registered. When checking a specific function, the priority of that
 *                   hook is returned, or false if the function is not attached. When using the
 *                   $function_to_check argument, this function may return a non-boolean value
 *                   that evaluates to false (e.g.) 0, so use the === operator for testing the
 *                   return value.
 */
function has_filter($tag, $function_to_check = false) {
	global $roster_filter;

	if ( ! isset( $roster_filter[ $tag ] ) ) {
		return false;
	}

	return $roster_filter[ $tag ]->has_filter( $tag, $function_to_check );
}

/**
 * Call the functions added to a filter hook.
 *
 * The callback functions attached to filter hook $tag are invoked by calling
 * this function. This function can be used to create a new filter hook by
 * simply calling this function with the name of the new hook specified using
 * the $tag parameter.
 *
 * The function allows for additional arguments to be added and passed to hooks.
 *
 *     // Our filter callback function
 *     function example_callback( $string, $arg1, $arg2 ) {
 *         // (maybe) modify $string
 *         return $string;
 *     }
 *     add_filter( 'example_filter', 'example_callback', 10, 3 );
 *
 *     /*
 *      * Apply the filters by calling the 'example_callback' function we
 *      * "hooked" to 'example_filter' using the add_filter() function above.
 *      * - 'example_filter' is the filter hook $tag
 *      * - 'filter me' is the value being filtered
 *      * - $arg1 and $arg2 are the additional arguments passed to the callback.
 *     $value = apply_filters( 'example_filter', 'filter me', $arg1, $arg2 );
 *
 * @since 0.71
 *
 * @global array $roster_filter         Stores all of the filters.
 * @global array $roster_current_filter Stores the list of current filters with the current one last.
 *
 * @param string $tag     The name of the filter hook.
 * @param mixed  $value   The value on which the filters hooked to `$tag` are applied on.
 * @param mixed  $var,... Additional variables passed to the functions hooked to `$tag`.
 * @return mixed The filtered value after all hooked functions are applied to it.
 */
function apply_filters( $tag, $value ) {
	global $roster_filter, $roster_current_filter;

	$args = array();

	// Do 'all' actions first.
	if ( isset($roster_filter['all']) ) {
		$roster_current_filter[] = $tag;
		$args = func_get_args();
		_roster_call_all_hook($args);
	}

	if ( !isset($roster_filter[$tag]) ) {
		if ( isset($roster_filter['all']) )
			array_pop($roster_current_filter);
		return $value;
	}

	if ( !isset($roster_filter['all']) )
		$roster_current_filter[] = $tag;

	if ( empty($args) )
		$args = func_get_args();

	// don't pass the tag name to Roster_Hook
	array_shift( $args );

	$filtered = $roster_filter[ $tag ]->apply_filters( $value, $args );

	array_pop( $roster_current_filter );

	return $filtered;
}

/**
 * Execute functions hooked on a specific filter hook, specifying arguments in an array.
 *
 * @since 3.0.0
 *
 * @see apply_filters() This function is identical, but the arguments passed to the
 * functions hooked to `$tag` are supplied using an array.
 *
 * @global array $roster_filter         Stores all of the filters
 * @global array $roster_current_filter Stores the list of current filters with the current one last
 *
 * @param string $tag  The name of the filter hook.
 * @param array  $args The arguments supplied to the functions hooked to $tag.
 * @return mixed The filtered value after all hooked functions are applied to it.
 */
function apply_filters_ref_array($tag, $args) {
	global $roster_filter, $roster_current_filter;

	// Do 'all' actions first
	if ( isset($roster_filter['all']) ) {
		$roster_current_filter[] = $tag;
		$all_args = func_get_args();
		_roster_call_all_hook($all_args);
	}

	if ( !isset($roster_filter[$tag]) ) {
		if ( isset($roster_filter['all']) )
			array_pop($roster_current_filter);
		return $args[0];
	}

	if ( !isset($roster_filter['all']) )
		$roster_current_filter[] = $tag;

	$filtered = $roster_filter[ $tag ]->apply_filters( $args[0], $args );

	array_pop( $roster_current_filter );

	return $filtered;
}

/**
 * Removes a function from a specified filter hook.
 *
 * This function removes a function attached to a specified filter hook. This
 * method can be used to remove default functions attached to a specific filter
 * hook and possibly replace them with a substitute.
 *
 * To remove a hook, the $function_to_remove and $priority arguments must match
 * when the hook was added. This goes for both filters and actions. No warning
 * will be given on removal failure.
 *
 * @since 1.2.0
 *
 * @global array $roster_filter         Stores all of the filters
 *
 * @param string   $tag                The filter hook to which the function to be removed is hooked.
 * @param callable $function_to_remove The name of the function which should be removed.
 * @param int      $priority           Optional. The priority of the function. Default 10.
 * @return bool    Whether the function existed before it was removed.
 */
function remove_filter( $tag, $function_to_remove, $priority = 10 ) {
	global $roster_filter;

	$r = false;
	if ( isset( $roster_filter[ $tag ] ) ) {
		$r = $roster_filter[ $tag ]->remove_filter( $tag, $function_to_remove, $priority );
		if ( ! $roster_filter[ $tag ]->callbacks ) {
			unset( $roster_filter[ $tag ] );
		}
	}

	return $r;
}

/**
 * Remove all of the hooks from a filter.
 *
 * @since 2.7.0
 *
 * @global array $roster_filter  Stores all of the filters
 *
 * @param string   $tag      The filter to remove hooks from.
 * @param int|bool $priority Optional. The priority number to remove. Default false.
 * @return true True when finished.
 */
function remove_all_filters( $tag, $priority = false ) {
	global $roster_filter;

	if ( isset( $roster_filter[ $tag ]) ) {
		$roster_filter[ $tag ]->remove_all_filters( $priority );
		if ( ! $roster_filter[ $tag ]->has_filters() ) {
			unset( $roster_filter[ $tag ] );
		}
	}

	return true;
}

/**
 * Retrieve the name of the current filter or action.
 *
 * @since 2.5.0
 *
 * @global array $roster_current_filter Stores the list of current filters with the current one last
 *
 * @return string Hook name of the current filter or action.
 */
function current_filter() {
	global $roster_current_filter;
	return end( $roster_current_filter );
}

/**
 * Retrieve the name of the current action.
 *
 * @since 3.9.0
 *
 * @return string Hook name of the current action.
 */
function current_action() {
	return current_filter();
}

/**
 * Retrieve the name of a filter currently being processed.
 *
 * The function current_filter() only returns the most recent filter or action
 * being executed. did_action() returns true once the action is initially
 * processed.
 *
 * This function allows detection for any filter currently being
 * executed (despite not being the most recent filter to fire, in the case of
 * hooks called from hook callbacks) to be verified.
 *
 * @since 3.9.0
 *
 * @see current_filter()
 * @see did_action()
 * @global array $roster_current_filter Current filter.
 *
 * @param null|string $filter Optional. Filter to check. Defaults to null, which
 *                            checks if any filter is currently being run.
 * @return bool Whether the filter is currently in the stack.
 */
function doing_filter( $filter = null ) {
	global $roster_current_filter;

	if ( null === $filter ) {
		return ! empty( $roster_current_filter );
	}

	return in_array( $filter, $roster_current_filter );
}

/**
 * Retrieve the name of an action currently being processed.
 *
 * @since 3.9.0
 *
 * @param string|null $action Optional. Action to check. Defaults to null, which checks
 *                            if any action is currently being run.
 * @return bool Whether the action is currently in the stack.
 */
function doing_action( $action = null ) {
	return doing_filter( $action );
}

/**
 * Hooks a function on to a specific action.
 *
 * Actions are the hooks that the WordPress core launches at specific points
 * during execution, or when specific events occur. Plugins can specify that
 * one or more of its PHP functions are executed at these points, using the
 * Action API.
 *
 * @since 1.2.0
 *
 * @param string   $tag             The name of the action to which the $function_to_add is hooked.
 * @param callable $function_to_add The name of the function you wish to be called.
 * @param int      $priority        Optional. Used to specify the order in which the functions
 *                                  associated with a particular action are executed. Default 10.
 *                                  Lower numbers correspond with earlier execution,
 *                                  and functions with the same priority are executed
 *                                  in the order in which they were added to the action.
 * @param int      $accepted_args   Optional. The number of arguments the function accepts. Default 1.
 * @return true Will always return true.
 */
function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
	return add_filter($tag, $function_to_add, $priority, $accepted_args);
}

/**
 * Execute functions hooked on a specific action hook.
 *
 * This function invokes all functions attached to action hook `$tag`. It is
 * possible to create new action hooks by simply calling this function,
 * specifying the name of the new hook using the `$tag` parameter.
 *
 * You can pass extra arguments to the hooks, much like you can with apply_filters().
 *
 * @since 1.2.0
 *
 * @global array $roster_filter         Stores all of the filters
 * @global array $roster_actions        Increments the amount of times action was triggered.
 * @global array $roster_current_filter Stores the list of current filters with the current one last
 *
 * @param string $tag     The name of the action to be executed.
 * @param mixed  $arg,... Optional. Additional arguments which are passed on to the
 *                        functions hooked to the action. Default empty.
 */
function do_action($tag, $arg = '') {
	global $roster_filter, $roster_actions, $roster_current_filter;

	if ( ! isset($roster_actions[$tag]) )
		$roster_actions[$tag] = 1;
	else
		++$roster_actions[$tag];

	// Do 'all' actions first
	if ( isset($roster_filter['all']) ) {
		$roster_current_filter[] = $tag;
		$all_args = func_get_args();
		_roster_call_all_hook($all_args);
	}

	if ( !isset($roster_filter[$tag]) ) {
		if ( isset($roster_filter['all']) )
			array_pop($roster_current_filter);
		return;
	}

	if ( !isset($roster_filter['all']) )
		$roster_current_filter[] = $tag;

	$args = array();
	if ( is_array($arg) && 1 == count($arg) && isset($arg[0]) && is_object($arg[0]) ) // array(&$this)
		$args[] =& $arg[0];
	else
		$args[] = $arg;
	for ( $a = 2, $num = func_num_args(); $a < $num; $a++ )
		$args[] = func_get_arg($a);

	$roster_filter[ $tag ]->do_action( $args );

	array_pop($roster_current_filter);
}

/**
 * Retrieve the number of times an action is fired.
 *
 * @since 2.1.0
 *
 * @global array $roster_actions Increments the amount of times action was triggered.
 *
 * @param string $tag The name of the action hook.
 * @return int The number of times action hook $tag is fired.
 */
function did_action($tag) {
	global $roster_actions;

	if ( ! isset( $roster_actions[ $tag ] ) )
		return 0;

	return $roster_actions[$tag];
}

/**
 * Execute functions hooked on a specific action hook, specifying arguments in an array.
 *
 * @since 2.1.0
 *
 * @see do_action() This function is identical, but the arguments passed to the
 *                  functions hooked to $tag< are supplied using an array.
 * @global array $roster_filter         Stores all of the filters
 * @global array $roster_actions        Increments the amount of times action was triggered.
 * @global array $roster_current_filter Stores the list of current filters with the current one last
 *
 * @param string $tag  The name of the action to be executed.
 * @param array  $args The arguments supplied to the functions hooked to `$tag`.
 */
function do_action_ref_array($tag, $args) {
	global $roster_filter, $roster_actions, $roster_current_filter;

	if ( ! isset($roster_actions[$tag]) )
		$roster_actions[$tag] = 1;
	else
		++$roster_actions[$tag];

	// Do 'all' actions first
	if ( isset($roster_filter['all']) ) {
		$roster_current_filter[] = $tag;
		$all_args = func_get_args();
		_roster_call_all_hook($all_args);
	}

	if ( !isset($roster_filter[$tag]) ) {
		if ( isset($roster_filter['all']) )
			array_pop($roster_current_filter);
		return;
	}

	if ( !isset($roster_filter['all']) )
		$roster_current_filter[] = $tag;

	$roster_filter[ $tag ]->do_action( $args );

	array_pop($roster_current_filter);
}

/**
 * Check if any action has been registered for a hook.
 *
 * @since 2.5.0
 *
 * @see has_filter() has_action() is an alias of has_filter().
 *
 * @param string        $tag               The name of the action hook.
 * @param callable|bool $function_to_check Optional. The callback to check for. Default false.
 * @return bool|int If $function_to_check is omitted, returns boolean for whether the hook has
 *                  anything registered. When checking a specific function, the priority of that
 *                  hook is returned, or false if the function is not attached. When using the
 *                  $function_to_check argument, this function may return a non-boolean value
 *                  that evaluates to false (e.g.) 0, so use the === operator for testing the
 *                  return value.
 */
function has_action($tag, $function_to_check = false) {
	return has_filter($tag, $function_to_check);
}

/**
 * Removes a function from a specified action hook.
 *
 * This function removes a function attached to a specified action hook. This
 * method can be used to remove default functions attached to a specific filter
 * hook and possibly replace them with a substitute.
 *
 * @since 1.2.0
 *
 * @param string   $tag                The action hook to which the function to be removed is hooked.
 * @param callable $function_to_remove The name of the function which should be removed.
 * @param int      $priority           Optional. The priority of the function. Default 10.
 * @return bool Whether the function is removed.
 */
function remove_action( $tag, $function_to_remove, $priority = 10 ) {
	return remove_filter( $tag, $function_to_remove, $priority );
}

/**
 * Remove all of the hooks from an action.
 *
 * @since 2.7.0
 *
 * @param string   $tag      The action to remove hooks from.
 * @param int|bool $priority The priority number to remove them from. Default false.
 * @return true True when finished.
 */
function remove_all_actions($tag, $priority = false) {
	return remove_all_filters($tag, $priority);
}

function _roster_call_all_hook($args) {
	global $roster_filter;

	$roster_filter['all']->do_all_hook( $args );
}

function _roster_filter_build_unique_id($tag, $function, $priority) {
	global $roster_filter;
	static $filter_id_count = 0;

	if ( is_string($function) )
		return $function;

	if ( is_object($function) ) {
		// Closures are currently implemented as objects
		$function = array( $function, '' );
	} else {
		$function = (array) $function;
	}

	if (is_object($function[0]) ) {
		// Object Class Calling
		if ( function_exists('spl_object_hash') ) {
			return spl_object_hash($function[0]) . $function[1];
		} else {
			$obj_idx = get_class($function[0]).$function[1];
			if ( !isset($function[0]->roster_filter_id) ) {
				if ( false === $priority )
					return false;
				$obj_idx .= isset($roster_filter[$tag][$priority]) ? count((array)$roster_filter[$tag][$priority]) : $filter_id_count;
				$function[0]->roster_filter_id = $filter_id_count;
				++$filter_id_count;
			} else {
				$obj_idx .= $function[0]->roster_filter_id;
			}

			return $obj_idx;
		}
	} elseif ( is_string( $function[0] ) ) {
		// Static Calling
		return $function[0] . '::' . $function[1];
	}
}