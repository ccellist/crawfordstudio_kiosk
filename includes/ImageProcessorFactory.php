<?php
require_once "interfaces/ImageProcessor.php";
require_once "ImageGd2Processor.php";
require_once "ImageMagickProcessor.php";
require_once "ImageExifProcessor.php";
/**
 * Description of ImageProcessorFactory
 *
 * @author arturo
 */
class ImageProcessorFactory {
    public static function getImageProcessor($imgName, $imgType = "jpeg", $rotateAngle = 0){
        switch (IMAGE_PROCESSOR){
            case "GD2":
                return new ImageGd2Processor($imgName, $imgType, $rotateAngle);
                break;
            case "ImageMagick":
                return new ImageMagickProcessor($imgName, $imgType, $rotateAngle);
                break;
            case "Exif":
                return new ImageExifProcessor($imgName, $imgType, $rotateAngle);
                break;
            default:
                return new ImageGd2Processor($imgName, $imgType, $rotateAngle);
                break;
        }
    }
}

?>
