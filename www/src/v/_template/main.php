<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo isset($title) ? $title : 'Home Page'; ?></title>
	<?php headPrint(); ?>
</head>
<body>
	<?php echo $content; ?>
</body>
</html>