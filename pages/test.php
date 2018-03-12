<?php
echo '<div id="main" class="container">';


$query = "SELECT * FROM `" . $roster->db->table('user_members') . "` WHERE `usr`='ulminia#1676' LIMIT 1;";
		$result = $roster->db->query($query);
		//echo (bool)$result;
		$row = $roster->db->fetch($result);
d($row);

$q = "SELECT * FROM `" . $roster->db->table('user_members') . "` WHERE `usr`=:usr LIMIT 1";
          //WHERE id = :id";
      // Prepare the SQL query
      $sth = $roster->db->link_id->prepare($q);
      // Bind parameters to statement variables
	  $x = 'ulminia#1676';
      $sth->bindParam(':usr', $x);
      // Execute statement
      $sth->execute();
$sthx = $sth->fetchAll();
d($sthx);
//d($roster->locale->wordings);
/*
$wherexx = " ";//Where `guild_id` = '" . $roster->data['guild_id'] . "'";
	$queryxx = "SELECT `level`, `classid`, `guild_title` FROM `" . $roster->db->table('members') . "`"
	. $wherexx . " Order by `level` ASC";
	$resultxx = $roster->db->query($queryxx);
	
$dat = array();
$total = 0;
$f = array();

foreach($roster->locale->act['id_to_class'] as $class_id => $class)
{
	$dat[$class_id] = 0;
}

while ($rowx = $roster->db->fetch($resultxx))
{
	$dat[$rowx['classid']]++;
	$total++;

	if( $rowx['level'] <=9 )
	{
		$f['0']['name'] = '1 - 9';
		$f['0']['num']++;
	}
	elseif( $rowx['level'] >=10 && $rowx['level'] <=19 )
	{
		$f['1']['name'] = '10 - 19';
		$f['1']['num']++;
	}
	elseif( $rowx['level'] >=20 && $rowx['level'] <=29 )
	{
		$f['2']['name'] = '20 - 29';
		$f['2']['num']++;
	}
	elseif( $rowx['level'] >=30 && $rowx['level'] <=39 )
	{
		$f['3']['name'] = '30 - 39';
		$f['3']['num']++;
	}
	elseif( $rowx['level'] >=40 && $rowx['level'] <=49 )
	{
		$f['4']['name'] = '40 - 49';
		$f['4']['num']++;
	}
	elseif( $rowx['level'] >=50 && $rowx['level'] <=59 )
	{
		$f['5']['name'] = '50 - 59';
		$f['5']['num']++;
	}
	elseif( $rowx['level'] >=60 && $rowx['level'] <=69 )
	{
		$f['6']['name'] = '60 - 69';
		$f['6']['num']++;
	}
	elseif( $rowx['level'] >=70 && $rowx['level'] <=79 )
	{
		$f['7']['name'] = '70 - 79';
		$f['7']['num']++;
	}
	elseif( $rowx['level'] >=80 && $rowx['level'] <=89 )
	{
		$f['8']['name'] = '80 - 89';
		$f['8']['num']++;
	}
	elseif( $rowx['level'] >=90 && $rowx['level'] <=99 )
	{
		$f['9']['name'] = '90 - 99';
		$f['9']['num']++;
	}
	elseif( $rowx['level'] >=100 && $rowx['level'] <=109 )
	{
		$f['10']['name'] = '100 - 109';
		$f['10']['num']++;
	}
	elseif( $rowx['level'] == ROSTER_MAXCHARLEVEL )
	{
		$f['11']['name'] = ROSTER_MAXCHARLEVEL;
		$f['11']['num']++;
	}
}

//d($dat);
echo 'im a test page<br>';

echo '<div class="row">';
foreach($roster->locale->act['id_to_class'] as $class_id => $class)
{
	$per = ($dat[$class_id] / $total * 100);
	echo '<div class="col-lg-1">
			<div id="class_'.$class_id.'" class="vertical-box-outter"><div class="text">'.$dat[$class_id].'</div>
				<div class="vertical-box-inner" style="background-color: #'.$roster->locale->act['class_colorArray'][$class].';height:'.$per.'px;"></div>
			</div>
		</div>';
}
echo '</div>';

echo '<div class="row">';
foreach($roster->locale->act['id_to_class'] as $class_id => $class)
{
	echo '<div class="col-lg-1 text-center">'.$class.'</div>';
}
echo '</div>';

	// levels

echo '<div class="row">';
foreach($f as $r => $d)
{
	$per = ($d['num'] / $total * 100);
	echo '<div class="col-lg-1">
			<div id="level_'.$r.'" class="vertical-box-outter"><div class="text">'.$d['num'].'</div>
				<div class="vertical-box-inner" style="background-color: #'.$roster->locale->act['class_colorArray'][$class].';height:'.$per.'px;"></div>
			</div>
		</div>';
}
echo '</div>';

echo '<div class="row">';
foreach($f as $r => $d)
{
	echo '<div class="col-lg-1 text-center">'.$d['name'].'</div>';
}
echo '</div>';



*/
echo '</div>';