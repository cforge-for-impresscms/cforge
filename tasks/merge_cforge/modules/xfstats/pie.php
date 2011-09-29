<?php
	// create image
	 
	 
	$width = 200;
	$height = 100;
	$font = 5;
	$image = imagecreate($width, $height);
	 
	$white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
	$green = imagecolorallocate($image, 0x00, 0xF0, 0x00);
	$red = imagecolorallocate($image, 0xFF, 0x00, 0x00);
	$black = imagecolorallocate($image, 0x00, 0x00, 0x00);
	 
	imagefillarc($image, $width/2, $height, $width, $width, 180, 180+($nn * 180/$total) , $green);
	imagefillarc($image, $width/2, $height, $width, $width, 180+($nn * 180/$total), 360, $red);
	imagestring ($image, $font, 5, $height-15, $nn, $black);
	imagestring ($image, $font, $width-5-(10 * strlen($total-$nn)), $height-15, ($total-$nn), $black);
	// flush image
	header("Content-type: image/png");
	imagepng($image);
	imagedestroy($image);
	 
	function imagefillarc($Image, $CenterX, $CenterY, $DiameterX, $DiameterY, $Start, $End, $Color)
	{
		// To draw the arc
		imagearc($Image, $CenterX, $CenterY, $DiameterX, $DiameterY, $Start, $End, $Color);
		// To close the arc with 2 lines between the center and the 2 limits of the arc
		$x = $CenterX + (cos(deg2rad($Start)) * ($DiameterX/2));
		$y = $CenterY + (sin(deg2rad($Start)) * ($DiameterY/2));
		imageline($Image, $x, $y, $CenterX, $CenterY, $Color);
		$x = $CenterX + (cos(deg2rad($End)) * ($DiameterX/2));
		$y = $CenterY + (sin(deg2rad($End)) * ($DiameterY/2));
		imageline($Image, $x, $y, $CenterX, $CenterY, $Color);
		// To fill the arc, the starting point is a point in the middle of the closed space
		$x = $CenterX + (cos(deg2rad(($Start+$End)/2)) * ($DiameterX/4));
		$y = $CenterY + (sin(deg2rad(($Start+$End)/2)) * ($DiameterY/4));
		imagefilltoborder($Image, $x, $y, $Color, $Color);
	}
	 
	 
?>