<?php
if (isset($opt['message']))
	$message = $opt['message'];
?>
<?php if (isset($message)): ?>
	<p><?php echo $message; ?></p>
<?php else: ?>
	<p>Sorry, no messages for you!</p>
<?php endif ?>

<?php run('bye'); ?>