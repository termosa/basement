<a href="<?php lnk('bye', array('name'=>'men')); ?>">bye</a>
<h4>Good bye<?php if (isset($_GET['name'])) echo ', ' . $_GET['name']; ?>!</h4>