<?php

require_once "ImageProcessorFactory.php";
define("BASE_PATH", "/var/www/CrawfordPhotoServer");
require_once "config.php";
require_once "MemcacheTool.php";

if (get_magic_quotes_runtime())
    set_magic_quotes_runtime(false);

$memcache = MemcacheTool::getMemcache();

$flushMemcache = @$_GET['flush'];
if ($flushMemcache == 1)
    $memcache->flush();
$photoUrl = $_GET['u'];
$rotateAngle = $_GET['r'];
$fullSize = @$_GET['full'];
$imageProcessor = ImageProcessorFactory::getImageProcessor($photoUrl, 'jpeg', $rotateAngle);
$processorUsed = get_class($imageProcessor);
$width = 0;
$height = 0;

if ($fullSize == 1) {
    if ($processorUsed == "ImageMagickProcessor") {
        $imageProcessor->setRotation(-$rotateAngle * 2);
        $width = 630;
        $height = 630;
    } elseif ($processorUsed == "ImageExifProcessor") {
        $rotateAngle = $imageProcessor->rotateAngle; //We override whatever angle
        // was passed via _GET by reading the orientation directly
        // from exif tags.
        $imageProcessor = new ImageGd2Processor($photoUrl, 'jpeg', $rotateAngle);
        $width = 450;
        $height = 630;
    } else {
        $width = 450;
        $height = 630;
    }
    // We're not actually showing a full size photo, only a bigger
    // thumbnail. The full size photo would have a much higher
    // resolution.
} else {
    if ($processorUsed == "ImageMagickProcessor") {
        $imageProcessor->setRotation(-$rotateAngle * 2);
        $width = 140;
        $height = 140;
    } else {
        $width = 100;
        $height = 140;
    }
}
if ($rotateAngle == 0) {
    if (($picture = $memcache->get($photoUrl . "_" . $height)) === false) {
        $picture = $imageProcessor->makeThumbnail($height, $width, true);
        $memcache->set($photoUrl . "_" . $height, $picture, false, 0);
    }
} else {
    //Assume portrait
    if (($picture = $memcache->get($photoUrl . "_" . $width)) === false) {
        $picture = $imageProcessor->makeThumbnail($width, $height, true);
        $memcache->set($photoUrl . "_" . $width, $picture, false, 0);
    }
}

header('Content-Type: image/jpeg');
print $picture;
//var_dump($picture);
?>
