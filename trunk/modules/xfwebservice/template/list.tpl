<?php echo '<list'
	.($name ? ' name="'.$name.'"' : '')
	.($contents ? '>'.$contents.'</list>' : '/>');
?>