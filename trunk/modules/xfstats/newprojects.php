<?php
   // PhpBarGraph Example Version 2.3
   // Bar Graph Generator Example for PHP
   // Written By TJ Hunter (tjhunter@ruistech.com)
   // Released Under the GNU Public License.
   // http://www.ruistech.com/phpBarGraph

   $img = "png";

   // We need to be able to use the bar graph class in phpBarGraph2.php
   @include_once("include/phpBarGraph2.php");

   if (!class_exists("PhpBarGraph"))
   {
      die("There was an error loading the PhpBarGraph class.");
   }

   // Setup how high and how wide the ouput image is
   $imageHeight = 200;
   $imageWidth = 320;

   // Create a new Image
   $image = ImageCreate($imageWidth, $imageHeight);

   // Fill it with your favorite background color..
   $backgroundColor = ImageColorAllocate($image, 0xff, 0xff, 0xff);
   ImageFill($image, 0, 0, $backgroundColor);

   // Interlace the image..
   //Imageinterlace($image, 1);


   // Create a new BarGraph..
   $myBarGraph = new PhpBarGraph;
   $myBarGraph->SetX(10);              // Set the starting x position
   $myBarGraph->SetY(10);              // Set the starting y position
   $myBarGraph->SetWidth($imageWidth-20);    // Set how wide the bargraph will be
   $myBarGraph->SetHeight($imageHeight-20);  // Set how tall the bargraph will be
   $myBarGraph->SetNumOfValueTicks(4); // Set this to zero if you don't want to show any. These are the vertical bars to help see the values.
   
   // You can try uncommenting these lines below for different looks.
   
   // $myBarGraph->SetShowLabels(false);  // The default is true. Setting this to false will cause phpBarGraph to not print the labels of each bar.
   // $myBarGraph->SetShowValues(false);  // The default is true. Setting this to false will cause phpBarGraph to not print the values of each bar.
   // $myBarGraph->SetBarBorder(false);   // The default is true. Setting this to false will cause phpBarGraph to not print the border of each bar.
   $myBarGraph->SetShowFade(false);    // The default is true. Setting this to false will cause phpBarGraph to not print each bar as a gradient.
   // $myBarGraph->SetShowOuterBox(false);   // The default is true. Setting this to false will cause phpBarGraph to not print the outside box.
   $myBarGraph->SetBarSpacing(20);     // The default is 10. This changes the space inbetween each bar.

   // Add Values to the bargraph..
   $myBarGraph->AddValue("Today",$bar[0]);
   $myBarGraph->AddValue("7 Days",$bar[1]);  // AddValue(string label, int value)
   $myBarGraph->AddValue("30 Days",$bar[2]);
   $myBarGraph->AddValue("90 Days",$bar[3]);

   // Set the colors of the bargraph..
   $myBarGraph->SetStartBarColor("0000ff");  // This is the color on the top of every bar.
//   $myBarGraph->SetEndBarColor("0000ff");    // This is the color on the bottom of every bar. This is not used when SetShowFade() is set to false.
   $myBarGraph->SetLineColor("000000");      // This is the color all the lines and text are printed out with.

   // Print the BarGraph to the image..
   $myBarGraph->DrawBarGraph($image);


   // Output the Image to the browser in GIF or PNG format
   if ($img == "png")
   {
      header("Content-type: image/png");
      ImagePNG($image);
   }
   elseif ($img == "gif")
   {
      header("Content-type: image/gif");
      ImageGif($image);
   }

   // Destroy the image.
   Imagedestroy($image);


?> 