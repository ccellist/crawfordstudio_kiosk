<?php

/**
 * Class that processes images using ImageMagick, 
 * a faster image processing API than GD2.
 *
 * @author Arturo Araya
 */
class ImageMagickProcessor implements Iface_ImageProcessor {

    private $imgName;
    private $imgSrc;
    private $imgType;
    private $imgWidth;
    private $imgHeight;
    private $resImg;
    private $isValid = false;
    private static $img_count = 0;
    private static $img_dir;
    private static $rel_dir;

    public function __construct($imgName, $imgType = "jpeg", $rotateAngle = 0) {
        $imgName = preg_replace("/\\\\/", "\\", $imgName);
        self::$img_dir = preg_replace("/\\\\/", "\\", self::$img_dir);
        if (strpos($imgName, DIRECTORY_SEPARATOR) !== false) {
            $tmp = explode("\\", $imgName);
            $imgName = array_pop($tmp);
            if (DIRECTORY_SEPARATOR == "\\") {
                self::$rel_dir = implode("\\", $tmp);
            } else {
                self::$rel_dir = DIRECTORY_SEPARATOR . implode("\\", $tmp);
            }
        } else {
            if (!isset(self::$img_dir)) {
                self::$img_dir = BASE_PATH . IMG_DIR;
            }
            $tmp = explode("\\", self::$img_dir);
            self::$rel_dir = $tmp[count($tmp) - 1];
        }
        if ($imgName == "")
            $imgName = "no_name.jpg";
        $this->imgName = $imgName;
        $this->imgSrc = self::$rel_dir . DIRECTORY_SEPARATOR . $this->imgName;
        $this->imgSrc = preg_replace("/\/\//", "/", $this->imgSrc);
        $this->imgType = $imgType;
        try {
            $cwd = getcwd();
            if (self::$rel_dir != null) {
                chdir(self::$rel_dir);
            } else {
                chdir(self::$img_dir);
            }
            if (file_exists($imgName) && is_readable($imgName)) {
                $this->resImg = new Imagick($this->imgSrc);
                $this->isValid = true;
                if ($rotateAngle > 0) {
                    $this->resImg->rotateimage(new ImagickPixel('none'), $rotateAngle);
                } else {
                    $this->rotateImage();
                }
                $this->imgWidth = $this->resImg->getimagewidth();
                $this->imgHeight = $this->resImg->getimageheight();
            } else {
                $this->resImg = $this->createBlankImg(630, 450);
            }
            chdir($cwd);
        } catch (Exception $e) {
            $this->isValid = false;
        }
    }

    public function setRotation($rotateAngle) {
        $this->resImg->rotateimage(new ImagickPixel("none"), $rotateAngle);
    }

    private function rotateImage() {
        $imageOrientation = $this->resImg->getImageOrientation();
        switch ($imageOrientation) {
            case 1:
                //do nothing
                break;
            case 2:
                $this->resImg->rotateimage(new ImagickPixel("none"), 90.0);
                break;
            case 3:
                $this->resImg->rotateimage(new ImagickPixel("none"), 180.0);
                break;
            case 4:
                $this->resImg->rotateimage(new ImagickPixel("none"), 270.0);
                break;
            case 5:
                //do nothing
                break;
            case 6:
                $this->resImg->rotateimage(new ImagickPixel("none"), 90.0);
                break;
            case 7:
                $this->resImg->rotateimage(new ImagickPixel("none"), 180.0);
                break;
            case 8:
                $this->resImg->rotateimage(new ImagickPixel("none"), 270.0);
                break;
            default:
                //do nothing
                break;
        }
    }

    public function createImg() {
        
    }

    public function editImg() {
        
    }

    public function getImg() {
        return $this->resImg;
    }

    public function getProp($prop) {
        return $this->$prop;
    }

    public function getWidth() {
        return $this->imgWidth;
    }

    public function getHeight() {
        return $this->imgHeight;
    }

    public function getImgDir() {
        return $this->img_dir;
    }

    public static function setImgDir($newdir) {
        self::$img_dir = $newdir;
    }

    public function makeThumbnail($nw, $nh, $saveToVariable = false, $destNameOverride = "", $imgTypeOverride = "") {
        try {
            if ($this->isValid) {
                if (strlen($imgTypeOverride) > 0) {
                    $imgType = $imgTypeOverride;
                } else {
                    $imgType = $this->imgType;
                }
                ($destNameOverride == "") || ($destNameOverride == $this->imgName) ?
                                $destName = preg_replace("/\.([a-z]+)*/", "_tn.$1", $this->imgName) :
                                $destName = $this->imgName;
                if (!$this->resImg->thumbnailImage($nw, $nh, true)) {
                    throw new Exception("Unable to thumbnail image.");
                }
                if (!$saveToVariable) {
                    return self::writeImg($this->resImg, $imgType, $destName, self::$img_dir);
                } else {
                    return self::writeImgToVar($this->resImg, $imgType);
                }
            } else {
                throw new Exception("Image not found.");
            }
        } catch (Exception $e) {
            $tmpImg = self::createBlankImg($nw, $nh, $saveToVariable);
            return $tmpImg->getImageBlob();
        }
    }

    public function saveImg($saveToVariable = false, $destNameOverride = "", $imgTypeOverride = "") {
        if (strlen($imgTypeOverride) > 0) {
            $imgType = $imgTypeOverride;
        } else {
            $imgType = $this->imgType;
        }
        ($destNameOverride == "") || ($destNameOverride == $this->imgName) ? $destName = preg_replace("/\.([a-z]+)*/", "_new.$1", $this->imgName) : $destName = $this->imgName;
        if (!$saveToVariable) {
            return self::writeImg($this->resImg, $imgType, $destName, self::$img_dir);
        } else {
            return self::writeImgToVar($this->resImg, $imgType);
        }
    }

    public static function createBlankImg($width, $height, $saveToVariable = false, $imgText = "Image not found.", $imgName = "") {
        $draw = new ImagickDraw();
        $im = new Imagick();
        $draw->setFont('Arial');
        $draw->setFontSize(24);
        $draw->setFillColor(new ImagickPixel('#000000'));
        $draw->setStrokeAntialias(true);
        $draw->setTextAntialias(true);
        $metrix = $im->queryFontMetrics($draw, $imgText);

        $draw->annotation(0, 25, $imgText);

        $im->newImage($width, $height, new ImagickPixel('white'));
        $im->drawImage($draw);
        $im->borderImage(new ImagickPixel('black'), 1, 1);
        $im->setImageFormat('png');
        return $im;
    }

    public static function writeImg($img, $imgType, $destName, $imgdir = "") {
        $cwd = getcwd();
        chdir($imgdir);
        $currImgType = $img->getFormat();
        if ($currImgType != $imgType) {
            $img->setFormat($imgType);
        }
        $img->writeimage($destName);
        chdir($cwd);
        return IMG_DIR . DIRECTORY_SEPARATOR . $destName;
    }

    public static function writeImgToVar($img, $imgType) {
        $currImgType = $img->getFormat();
        if ($currImgType != $imgType) {
            $img->setFormat($imgType);
        }
        $output = $img->getImageBlob();
        return $output;
    }

}

?>
