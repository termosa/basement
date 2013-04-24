<?php
function formAction($action = '.') {
	if (($link = parsePath($action, '/')) == '.') {
		global $_request;
		$link = substr($_request, 1);
	}
	printf('<input type="hidden" name="r" value="%s" />', $link);
}