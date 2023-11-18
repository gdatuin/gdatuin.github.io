<?php
session_start();

// Generate a random string.
$captcha_code = '';
for ($i = 0; $i < 6; $i++) {
    $captcha_code .= chr(rand(97, 122));
}
$_SESSION["captcha_code"] = $captcha_code;

// Create a CAPTCHA image.
$image = imagecreate(120, 40);
$background = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);

// Write the random string to the image.
imagestring($image, 5, 10, 10, $captcha_code, $text_color);

// Send headers and output the image.
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);
?>