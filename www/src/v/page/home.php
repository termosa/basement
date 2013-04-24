<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Done!</title>
</head>
<body>
	<h1><?php echo $msg; ?></h1>

	<fieldset>
		<legend>block/message</legend>
		<?php $getMessage = run('/block/message', array('sun', 'ice', 'grass'), JUST_C); ?>

		<?php run('/block/message', array('message'=>"Someone sent it to you: {$getMessage}"), JUST_V); ?>
	</fieldset>
</body>
</html>