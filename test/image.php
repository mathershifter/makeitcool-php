#!/opt/webstack/bin/php
<?php
/**
 * Simple image copier, rotator, and resizer 
 * 
 * Usage: image.php <source image> <dest image> <width> <height> <degrees>
 */
require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();

/* die early if not enough arguments */
if ($argc < 3) {
    echo "No image given\n";
    echo "Usage: " . basename($argv[0]) . " <source image> <dest image> <width> <height> <degrees>\n";
    die();
}

$source_image = $argv[1];
$dest_image   = $argv[2];
$degrees      = isset($argv[3]) ? $argv[3] : 0;
$width        = isset($argv[4]) ? $argv[4] : false;
$height       = isset($argv[5]) ? $argv[5] : false;


$image = Mic_Image::factory($source_image);

// resize if width and height are greater than zero
if ($width  > 0 && $height > 0) {
    $image->resize($width, $height);   
}

// rotate if degrees is non-zero
if (is_numeric($degrees) && $degrees !== 0) {
    $image->rotate($degrees);
}

// save the image
$image->write($dest_image);