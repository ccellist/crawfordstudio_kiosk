<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FileWriter
 *
 * @author AA94427
 */
class FileWriter {
    private $fileName;
    private $filePath;
    private $fHandle;
    
    public function getFileName() {
        return $this->fileName;
    }

    public function setFileName($fileName) {
        $this->fileName = $fileName;
    }

    public function getFilePath() {
        return $this->filePath;
    }

    public function setFilePath($filePath) {
        $this->filePath = $filePath;
    }

    private function __construct($filePath, $fileName) {
        $this->fileName = $fileName;
        $this->filePath = $filePath;
        $this->fHandle = fopen($filePath . DIRECTORY_SEPARATOR . $fileName, "a");
    }
    
    private function writeLine($line){
        fwrite($this->fHandle, $line . "\r\n");
    }
    
    private function __destruct(){
        fclose($this->fHandle);
    }

    public static function writeLineToFile($line, $filepath, $fileName){
        $fileWriter = new FileWriter($filepath, $fileName);
        $fileWriter->writeLine($line);
        //$fileWriter->__destruct();
    }
}

?>
