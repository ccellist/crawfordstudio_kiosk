<?php

class Mock_Image extends Image{
    public function saveImg(){
        return $this->imgSrc;
    }
}

?>
