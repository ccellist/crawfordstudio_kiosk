<?php

/**
 * Description of Photo
 *
 * @author arturo
 */
class Photo extends AppObject{
    private $photoId;
    private $photoName;
    private $photoThumbnail;
    private $photoUri;
    private $photoPrice;
    private $photoOrientation;
    private $eventId;
    
    const PORTRAIT = 1;
    const LANDSCAPE = 0;
    
    function __construct($photoName, $photoUri, $photoPrice, $photoOrientation = self::LANDSCAPE) {
        $this->photoName = $photoName;
        $this->photoUri = $photoUri;
        $this->photoPrice = $photoPrice;
        $this->photoOrientation = $photoOrientation;
    }
    
    public function __get($name){
        return $this->$name;
    }
    
    public function __set($name, $value){
        $this->$name = $value;
    }

}

