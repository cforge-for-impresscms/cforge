<?php echo '<build'
	.($id ? ' id="'.$id.'"' : '')
	.($project ? ' project="'.$project.'"' : '')
	.($modules ? ' modules="'.$modules.'"' : '')
	.($target ? ' target="'.$target.'"' : '')
	.($status ? ' status="'.$status.'"' : '')
	.($elapsed ? ' elapsed="'.$elapsed.'"' : '')
	.($start ? ' start="'.$start.'"' : '')
	.($end ? ' end="'.$end.'"' : '')
	.($contents ? '>'.$contents.'</build>' : '/>');
?>