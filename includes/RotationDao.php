<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RotationDao
 *
 * @author arturo
 */
class RotationDao extends Dao {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Gets rotation list by name. Returns list of Rotation objects.
     * @param string $name
     */
    public function getRotationsByName($name) {
        $this->updsql = "select * from rotations where rotation_name = '$name'";
        $res = $this->retrieve();
        if ($res != null) {
            $rotationList = array();
            foreach ($res as $record) {
                $rotation = new Rotation($record["uid"], $record["rotation_name"]);
                $rotationList[] = $rotation;
            }
        }
        return $rotationList;
    }
    
    /**
     * Gets single rotation object by id.
     * 
     * @param int $id
     */
    public function getRotationById($id){
        $rotation = null;
        $this->updsql = "select * from rotations where uid = $id";
        $res = $this->retrieve();
        if ($res != null) {
            $rotation = new Rotation($res[0]["uid"],$res[0]["rotation_name"]);
        }
        return $rotation;
    }

}

?>
