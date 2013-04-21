<?php

if ( ! count( $this->_parent )) {
	$this->message = 'I have no options yet!';
	return 'Please, send me some options.';
}

$this->message = "I took this options: '"
	. implode( "', '", $this->_parent ) . "'.";

return 'I have ' . count( $this->_parent ) . ' options. Thanks man!';