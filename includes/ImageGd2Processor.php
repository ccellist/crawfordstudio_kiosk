<?php

class ImageGd2Processor implements Iface_ImageProcessor {

    private $imgName;
    private $imgSrc;
    private $imgType;
    private $imgWidth;
    private $imgHeight;
    private $resImg; /* gd2 image resource */
    private $isValid = false;
    private $rotateAngle;
    private static $img_count = 0;
    private static $img_dir;
    private static $rel_dir;

    public function __construct($imgName, $imgType = "jpeg", $rotateAngle = 0) {
        if (strpos($imgName, DIRECTORY_SEPARATOR) !== false) {
            $tmp = explode(DIRECTORY_SEPARATOR, $imgName);
            $imgName = array_pop($tmp);
            if (DIRECTORY_SEPARATOR == "\\") {
                self::$rel_dir = implode("\\", $tmp);
            } else {
                self::$rel_dir = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $tmp);
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
        $this->rotateAngle = $rotateAngle;
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
                $this->isValid = true;
            } else {
                $this->isValid = false;
            }
            chdir($cwd);
        } catch (Exception $e) {
            $this->isValid = false;
        }
    }

    protected function prepareImage() {
        $img = file_get_contents($this->imgSrc);
        $this->resImg = imagecreatefromstring($img);
        $this->isValid = true;
        if ($this->rotateAngle > 0) {
            $this->resImg = imagerotate($this->resImg, $this->rotateAngle, 0);
        }
        $this->imgWidth = imagesx($this->resImg);
        $this->imgHeight = imagesy($this->resImg);
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

    public function createImg() {
        
    }

    public function editImg() {
        
    }

    public function saveImg($saveToVariable = false, $destNameOverride = "", $imgTypeOverride = "") {
        $this->prepareImage();
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

    public function makeThumbnail($nw, $nh, $saveToVariable = false, $destNameOverride = "", $imgTypeOverride = "") {
        if ($this->isValid) {
            $this->prepareImage();
            if (strlen($imgTypeOverride) > 0) {
                $imgType = $imgTypeOverride;
            } else {
                $imgType = $this->imgType;
            }
            ($destNameOverride == "") || ($destNameOverride == $this->imgName) ?
                            $destName = preg_replace("/\.([a-z]+)*/", "_tn.$1", $this->imgName) :
                            $destName = $this->imgName;
            $w = $this->imgWidth;
            $h = $this->imgHeight;
            $thumb = imagecreatetruecolor($nw, $nh);
            imagecopyresampled($thumb, $this->resImg, 0, 0, 0, 0, $nw, $nh, $w, $h);
            if (!$saveToVariable) {
                return self::writeImg($thumb, $imgType, $destName, self::$img_dir);
            } else {
                return self::writeImgToVar($thumb, $imgType);
            }
        } else {
            $tmpImg = self::createBlankImg($nw, $nh, $saveToVariable);
            return $tmpImg;
        }
    }

    public static function writeImg($img, $imgType, $destName, $imgdir = "") {
        try {
            $cwd = getcwd();
            chdir($imgdir); //echo getcwd() . $destName . "<br>";
            switch ($imgType) {
                case "jpeg":
                    imagejpeg($img, $destName);
                    break;
                case "gif";
                    imagegif($img, $destName);
                    break;
                case "png":
                    imagepng($img, $destName);
                    break;
                default:
                    throw new Exception("Image type not supported.");
                    break;
            }            
            imagedestroy($img);
            //strlen($imgdir)>0 ? $writtenImg = $imgdir . "/" . $destName : $writtenImg = $destName;
            chdir($cwd);
            return IMG_DIR . DIRECTORY_SEPARATOR . $destName;
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";
            return false;
        }
    }

    public static function writeImgToVar($img, $imgType) {
        try {
            ob_start();
            switch ($imgType) {
                case "jpeg":
                    imagejpeg($img);
                    break;
                case "gif";
                    imagegif($img);
                    break;
                case "png":
                    imagepng($img);
                    break;
                default:
                    throw new Exception("Image type not supported.");
                    break;
            }
            $writtenImg = ob_get_contents();
            ob_end_clean();
            imagedestroy($img);
            return $writtenImg;
        } catch (Exception $e) {
            ob_end_clean();
            echo $e->getMessage() . "\n";
            return false;
        }
    }

    public static function createBlankImg($width, $height, $saveToVariable = false, $imgText = "", $imgName = "") {
        if (strlen($imgText) == 0) {
            $imgText = "No image available";
        }
        $arrText = explode(" ", $imgText);
        $img = imagecreatetruecolor($width, $height);
        $bc = imagecolorallocate($img, 255, 255, 255);
        $fc = imagecolorallocate($img, 0, 0, 0);

        imagefilledrectangle($img, 0, 0, $width, $height, $bc);
        $ycoord = 5;
        foreach ($arrText as $word) {
            $fldw = imagestring($img, 5, 5, $ycoord, $word, $fc);
            $ycoord += 15;
        }
        if (!$saveToVariable) {
            strlen($imgName) > 0 ? $destName = $imgName : $destName = "deflt_" . self::$img_count . ".jpg";
            $writtenImg = self::writeImg($img, "jpeg", $destName, self::$img_dir);
            self::$img_count++;
        } else {
            $writtenImg = self::writeImgToVar($img, "jpeg");
        }
        return $writtenImg;
    }

    public function __get($name) {
        return $this->$name;
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

}

?>