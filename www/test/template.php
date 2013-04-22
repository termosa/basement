<pre><?php

error_reporting(E_ALL);
ini_set('display_errors',1);


$mode = (!1) ? 'func' : 'var';


if ($mode == 'func') {

	$s = NULL;
	$s = memory_get_usage();

	function content() {
		include 'template/content.php';
	}

	include 'template/tpl_func.php';

	$t = memory_get_usage() - $s;

	echo $t;

}

if ($mode == 'var') {

	$s = NULL;
	$s = memory_get_usage();

	ob_start();
	include 'template/content.php';
	$content = ob_get_contents();
	ob_end_clean();

	include 'template/tpl_var.php';

	$t = memory_get_usage() - $s;

	echo $t;

}