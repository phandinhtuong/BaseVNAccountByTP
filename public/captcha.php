<?php
session_start();

$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ123456789';
$captcha = substr(str_shuffle($chars), 0, 6);
$_SESSION['captcha'] = $captcha;

header('Content-type: image/svg+xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<svg width="200" height="60" xmlns="http://www.w3.org/2000/svg">';
echo '<rect width="100%" height="100%" fill="#f5f5f5"/>';

// Add noise
for ($i = 0; $i < 50; $i++) {
    $x1 = rand(0, 200);
    $y1 = rand(0, 60);
    $x2 = rand(0, 200);
    $y2 = rand(0, 60);
    echo '<line x1="'.$x1.'" y1="'.$y1.'" x2="'.$x2.'" y2="'.$y2.'" stroke="#ccc" stroke-width="1"/>';
}

// Add text
for ($i = 0; $i < strlen($captcha); $i++) {
    $x = 20 + ($i * 30);
    $y = 40;
    $rotation = rand(-15, 15);
    echo '<text x="'.$x.'" y="'.$y.'" transform="rotate('.$rotation.' '.$x.','.$y.')" font-family="Arial" font-size="24" fill="#333">'.$captcha[$i].'</text>';
}

echo '</svg>';
?>