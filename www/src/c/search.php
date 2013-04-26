<?php

inc('Route_FormAction');
$s_query = (isset($_GET['search']) && isset($_GET['query'])) ?
	$_GET['query'] : '';