<?php

class PhotoCopy extends AppObject{
    private $photoName;
    private $srcPath;
    private $destPath;
    
    function __construct($photoName, $srcPath, $destPath) {
        $this->photoName = $photoName;
        $this->srcPath = $srcPath;
        $this->destPath = $destPath;
    }    
    
    public function __get($name){
        return $this->$name;
    }
    
    public function __set($name, $value){
        $this->$name = $value;
    }
}

?>
