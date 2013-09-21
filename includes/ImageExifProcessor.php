<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ImageExifProcessor
 *
 * @author AA94427
 */
define("MIRROR_HORIZONTAL", 2);
define("MIRROR_VERTICAL", 4);
define("MIRROR_BOTH", 3);

class ImageExifProcessor extends ImageGd2Processor {

    public function __construct($imgName, $imgType = "jpeg", $rotateAngle = 0) {
        parent::__construct($imgName, $imgType, $rotateAngle);
        $exifData = exif_read_data($imgName);
        $this->imgType = $exifData["FileType"];
    }

    public function makeThumbnail($nw, $nh, $saveToVariable = false, $destNameOverride = "", $imgTypeOverride = "") {
        if ($this->isValid) {
            if (strlen($imgTypeOverride) > 0) {
                $imgType = $imgTypeOverride;
            } else {
                $imgType = $this->imgType;
            }
            ($destNameOverride == "") || ($destNameOverride == $this->imgName) ?
                            $destName = preg_replace("/\.([a-z]+)*/", "_tn.$1", $this->imgName) :
                            $destName = $this->imgName;
            $thumb = exif_thumbnail($this->imgSrc);
            //$rotatedThumb = $thumb;
            $rotatedThumb = $this->fixOrientation($thumb);
            if (!$saveToVariable) {
                return self::writeImg($rotatedThumb, $imgType, $destName, self::$img_dir); //TODO: I'm not sure this works.
            } else {
                return $rotatedThumb;
            }
        } else {
            $tmpImg = self::createBlankImg($nw, $nh, $saveToVariable);
            return $tmpImg;
        }
    }

    private function flipImage($src, $type) {
        $imgsrc = $src;
        $width = imagesx($imgsrc);
        $height = imagesy($imgsrc);
        $imgdest = imagecreatetruecolor($width, $height);

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                if ($type == MIRROR_HORIZONTAL)
                    imagecopy($imgdest, $imgsrc, $width - $x - 1, $y, $x, $y, 1, 1);
                if ($type == MIRROR_VERTICAL)
                    imagecopy($imgdest, $imgsrc, $x, $height - $y - 1, $x, $y, 1, 1);
//                if ($type == MIRROR_BOTH)
//                    imagecopy($imgdest, $imgsrc, $width - $x - 1, $height - $y - 1, $x, $y, 1, 1);
            }
        }

        return $imgdest;
    }

    private function fixOrientation($image) {
        $output = null;
        $imgRes = imagecreatefromstring($image);
        $exif = exif_read_data($this->imgSrc);
        $ort = $exif['Orientation'];
        switch ($ort) {
            case 1: // nothing
                break;

            case 2: // horizontal flip
                $output = $this->flipImage($imgRes, 1);
                break;

            case 3: // 180 rotate left
                $output = imagerotate($imgRes, 180);
                $this->rotateAngle = 180.0;
                break;

            case 4: // vertical flip
                $output = $this->flipImage($imgRes, 2);
                break;

            case 5: // vertical flip + 90 rotate right
                $output = $this->flipImage($imgRes, 2);
                $output = rotateImage($output, -90);
                $this->rotateAngle = 270.0;
                break;

            case 6: // 90 rotate right
                $output = imagerotate($imgRes, -90);
                $this->rotateAngle = 270.0;
                break;

            case 7: // horizontal flip + 90 rotate right
                $output = $this->flipImage($imgRes, 1);
                $output = imagerotate($output, -90);
                $this->rotateAngle = 270.0;
                break;

            case 8:    // 90 rotate left
                $output = imagerotate($imgRes, 90, imageColorAllocateAlpha($imgRes, 0, 0, 0, 127));
                $this->rotateAngle = 90.0;
                break;
        }
        $mimeType = $exif["COMPUTED"]["Thumbnail.MimeType"];
        $tmp = explode("/", $mimeType);
        $imgType = $tmp[1];
        $return = ImageExifProcessor::writeImgToVar($output, $imgType);
        imagedestroy($imgRes);
        return $return;
    }

    public static function getPhotoRotateAngle($photoUri) {
        if (file_exists($photoUri)) {
            $exif = @exif_read_data($photoUri);
            if (array_key_exists("Orientation", $exif)) {
                $ort = $exif['Orientation'];
                switch ($ort) {
                    case 1: // nothing
                        return 0.0;

                    case 2: // horizontal flip
                        return 0.0;

                    case 3: // 180 rotate left
                        return 180.0;

                    case 4: // vertical flip
                        return 0.0;

                    case 5: // vertical flip + 90 rotate right
                        return 270.0;

                    case 6: // 90 rotate right
                        return 270.0;

                    case 7: // horizontal flip + 90 rotate right
                        return 270.0;

                    case 8:    // 90 rotate left
                        return 90.0;
                }
            }
        } else {
            return 0.0;
        }
    }

}

?>
