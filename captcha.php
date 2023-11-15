<?php

session_start();


header('Content-Type: image/jpeg');

function generateRandomString($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Create CAPTCHA string and store it in the session
$_SESSION['captcha'] = generateRandomString();

$captcha_image = imagecreatetruecolor(120, 40);
$background_color = imagecolorallocate($captcha_image, 220, 220, 220);
$text_color = imagecolorallocate($captcha_image, 0, 0, 0);
imagefill($captcha_image, 0, 0, $background_color);

$font = './Arial.ttf'; // Ensure this path is correct

// Check if the font file exists to avoid errors
if (!file_exists($font)) {
    die('The font file does not exist.');
}

imagettftext($captcha_image, 20, 0, 10, 30, $text_color, $font, $_SESSION['captcha']);

imagejpeg($captcha_image);
imagedestroy($captcha_image);
?>
