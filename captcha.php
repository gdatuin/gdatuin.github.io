<?php
session_start();


$randomString = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);

$_SESSION["captcha"] = $randomString;


header("Content-Type: image/png");
$image = imagecreatetruecolor(200, 50);


$background_color = imagecolorallocate($image, 255, 255, 255); 
$text_color = imagecolorallocate($image, 0, 0, 0); 
$line_color = imagecolorallocate($image, 64, 64, 64); 
$pixel_color = imagecolorallocate($image, 0, 0, 255); 


imagefilledrectangle($image, 0, 0, 200, 50, $background_color);


for($i = 0; $i < 5; $i++) {
    imageline($image, 0, rand()%50, 200, rand()%50, $line_color);
}

for($i = 0; $i < 1000; $i++) {
    imagesetpixel($image, rand()%200, rand()%50, $pixel_color);
}


imagettftext($image, 20, 0, 50, 30, $text_color, 'for_captcha/BebasNeue-Regular.ttf', $randomString);


imagepng($image);


imagedestroy($image);
?>
