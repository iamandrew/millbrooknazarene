<?php
// Writes a placeholder hero.png into the theme's images directory.
// Run from project root:
// php application/themes/millbrook/scripts/create_hero_png.php

$out = __DIR__ . '/../images/hero.png';

// Try to generate a nicer placeholder with GD (gradient + text). Fallback to a tiny transparent PNG if GD missing.
if (extension_loaded('gd')) {
    $w = 1600;
    $h = 900;
    $im = imagecreatetruecolor($w, $h);
    // Gradient from brand primary to brand dark
    $r1 = 41; $g1 = 143; $b1 = 194; // #298fc2
    $r2 = 53; $g2 = 72;  $b2 = 94;  // #35485e
    for ($y = 0; $y < $h; $y++) {
        $t = $y / ($h - 1);
        $r = (int)($r1 * (1 - $t) + $r2 * $t);
        $g = (int)($g1 * (1 - $t) + $g2 * $t);
        $b = (int)($b1 * (1 - $t) + $b2 * $t);
        $color = imagecolorallocate($im, $r, $g, $b);
        imageline($im, 0, $y, $w, $y, $color);
    }
    // Add white text roughly centered
    $white = imagecolorallocate($im, 255, 255, 255);
    $fontSize = 5; // imagestring font (no TTF dependency)
    $text1 = "Millbrook Church";
    $text2 = "A welcoming community — Join us this Sunday";
    $text1w = imagefontwidth($fontSize) * strlen($text1);
    $text2w = imagefontwidth($fontSize) * strlen($text2);
    imagestring($im, $fontSize, (int)(($w - $text1w) / 2), (int)($h * 0.4), $text1, $white);
    imagestring($im, $fontSize, (int)(($w - $text2w) / 2), (int)($h * 0.48), $text2, $white);

    if (imagepng($im, $out)) {
        imagedestroy($im);
        echo "Generated hero.png (GD) at: $out\n";
        exit(0);
    } else {
        imagedestroy($im);
        echo "Failed to write PNG with GD, attempting fallback...\n";
    }
}

// Fallback: write a 1x1 transparent PNG (smallest possible placeholder)
$base64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAASsJTYQAAAAASUVORK5CYII=';
$data = base64_decode($base64);
if (file_put_contents($out, $data) !== false) {
    echo "Wrote fallback hero.png to: $out\n";
    exit(0);
} else {
    echo "Failed to write fallback $out\n";
    exit(2);
}

?>
<?php
// Writes a placeholder hero.png into the theme's images directory.
// Run from project root: php application/themes/millbrook/scripts/create_hero_png.php

$out = __DIR__ . '/../images/hero.png';
$base64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAASsJTYQAAAAASUVORK5CYII='; // 1x1 transparent PNG
$data = base64_decode($base64);
if(file_put_contents($out, $data) !== false) {
    echo "Wrote hero.png to: $out\n";
} else {
    echo "Failed to write $out\n";
}
