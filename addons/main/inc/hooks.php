<?php
add_action('char_pre','my_function',10,1);

function my_function($data)
{
	echo $data['name'].'<br>';
}