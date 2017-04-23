<?php
$roster->output['show_header'] = false;
$roster->output['show_footer'] = false;

require_once ($addon['dir'] . 'inc/rsync_core.class.php');

$rsync = new rsync();

$e = $rsync->_getMembersToUpdate();
echo json_encode($e);