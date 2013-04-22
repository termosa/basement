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
		<?php $getMessage = run( 'block/message', array( 'sun', 'ice', 'grass' ), JUST_C ); ?>

		<?php if ( ! empty( $getMessage )): ?>
			<p><small>Someone sent it to you: '<?php echo $getMessage; ?>'</small></p>
		<?php endif ?>
	</fieldset>
</body>
</html>