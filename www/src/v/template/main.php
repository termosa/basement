<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo isset($this->title) ? $this->title : 'Home Page'; ?></title>
	<script type="text/javascript" src="<?php echo URL; ?>/i/script/jquery-1.7.1.js"></script>
</head>
<body>
	<?php echo $content; ?>
</body>
</html>