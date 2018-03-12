<?php

roster_add_css('templates/' . $roster->tpl->tpl . '/style/bootstrap/bootv4/css/bootstrap.min.css', 'theme'); //" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
roster_add_js('templates/' . $roster->tpl->tpl . '/style/bootstrap/bootv4/js/bootstrap.min.js', 'theme'); //integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
//$roster->output['html_head'] .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>';

add_action('roster_before_js','enqueue_popper',10);
function enqueue_popper()
{
	global $roster;
	echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>';
}


add_action('roster_before_js','enqueue_popper1',13);
add_action('roster_before_js','enqueue_popper2',14);
add_action('roster_before_js','enqueue_popper3',16);
add_action('roster_before_js','enqueue_popper4',11);
add_action('roster_before_js','enqueue_popper5',20);
add_action('roster_before_js','enqueue_popper6',12);
function enqueue_popper1(){	global $roster;	echo '<!-- 13 -->';}
function enqueue_popper2(){	global $roster;	echo '<!-- 14 -->';}
function enqueue_popper3(){	global $roster;	echo '<!-- 16 - 17 -->';}
function enqueue_popper4(){	global $roster;	echo '<!-- 18 -->';}
function enqueue_popper5(){	global $roster;	echo '<!-- 20 -->';}
function enqueue_popper6(){	global $roster;	echo '<!-- 22 -->';}