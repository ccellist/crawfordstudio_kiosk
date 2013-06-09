<?php

/**
 * Description of File
 *
 * @author arturo
 */
class PostFile implements Iface_FileHandler {

    private $_listOfFiles;

    public function __construct(array $fileList) {
        $this->_listOfFiles = $fileList;
    }

    public function validateFile() {
        if (is_array($this->_listOfFiles)) {
            if ((($this->listofFiles["file"]["type"] == "image/gif")
                    || ($this->listofFiles["file"]["type"] == "image/jpeg")
                    || ($this->listofFiles["file"]["type"] == "image/pjpeg"))
                    && ($this->listofFiles["file"]["size"] > 20000)) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function getFileName() {
        return $this->_listOfFiles["file"]["tmp_name"];
    }
}

