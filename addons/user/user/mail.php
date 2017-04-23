<?php

echo '<h1>im your mail box</h1>';



$roster->tpl->set_handle('mail', $addon['basename'] . '/mail.html');
$roster->tpl->display('mail');