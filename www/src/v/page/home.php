<?php
head('all', 'main');
head('print', 'typography');
head('js', 'main', 'jquery');
?>
<h1><?php echo $msg; ?></h1>

<fieldset>
	<legend>block/message</legend>
	<?php $getMessage = run('/_block/message', array('sun', 'ice', 'grass'), JUST_C); ?>

	<?php run('/_block/message', array('message'=>"Someone sent it to you: {$getMessage}"), JUST_V); ?>
</fieldset>