<?php echo '<publish'
	.($id ? ' id="'.$id.'"' : '')
	.($status ? ' status="'.$status.'"' : '')
	.($contents ? '>'.$contents.'</publish>' : '/>');
?>