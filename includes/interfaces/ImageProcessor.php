<?php
interface Iface_ImageProcessor {
    public function __construct($imgName, $imgType = "jpeg", $rotateAngle = 0);
    
    public function getImg();

    public function getProp($prop) ;

    public function getWidth() ;

    public function getHeight() ;

    public function getImgDir();

    public static function setImgDir($newdir);

    public function createImg();

    public function editImg();

    public function saveImg($saveToVariable = false, $destNameOverride = "", $imgTypeOverride = "") ;

    public function makeThumbnail($nw, $nh, $saveToVariable = false, $destNameOverride = "", $imgTypeOverride = "");

    public static function writeImg($img, $imgType, $destName, $imgdir = "");
    
    public static function writeImgToVar($img, $imgType);

    public static function createBlankImg($width, $height, $saveToVariable = false, $imgText = "", $imgName = "");
}

