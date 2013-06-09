<?php
define("BASE_PATH", dirname(__FILE__));
require_once(BASE_PATH . "/includes/config.php");

function __autoload($class) {
    if (preg_match("/^Mod_/", $class)) {
        $file = preg_replace("/^Mod_/", "", $class) . ".php";
        require_once(BASE_PATH . "/modules/" . $file);
    } elseif (preg_match("/^UI_/", $class)) {
        $file = preg_replace("/^UI_/", "", $class) . ".php";
        require_once(BASE_PATH . "/includes/UI/" . $file);
    } elseif (preg_match("/Exception$/", $class)) {
        require_once(BASE_PATH . "/includes/Exceptions/" . $class . ".php");
    } else {
        $file = str_replace("_", "/", preg_replace("/s$/", "", $class)) . ".php";
        $file = preg_replace("/Iface/", "interfaces", $file);
        require_once(BASE_PATH . "/includes/" . $file);
    }
}

$img = $_GET['i'];
$imgName = "/home/arturo/Pictures/CrawfordPhotoTest/$img";
$image = new ImageMagickProcessor($imgName);
?>
