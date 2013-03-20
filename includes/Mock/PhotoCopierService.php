<?php
class Mock_PhotoCopierService extends PhotoCopierService{
    public function doPhotoCopy(PhotoCopy $photoCopy){
        $this->normalizeDirectories($photoCopy);
        $src = $photoCopy->srcPath . "\\" . $photoCopy->photoName;
        $dest = $photoCopy->destPath . "\\" . $photoCopy->photoName;
        return sprintf("cp -va %s %s", $src, $dest);
    }
}

?>
