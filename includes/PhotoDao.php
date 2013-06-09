<?php

class PhotoDao extends Dao {

    public function __construct() {
        parent::__construct();
    }

    public function getPhotosByMeetEventId($eventId) {
        $output = array();
        $this->updsql = "select * from photos where event_id = $eventId order by uid";
        $res = $this->retrieve();
        if ($res != null) {
            foreach ($res as $key => $val) {
                $photo = new Photo($val['photo_name'], $val['photo_uri'], $val['photo_price'], $val['orient_portrait']);
                $photo->photoId = $val['uid'];
                $photo->photoThumbnail = $val['photo_thumbnail_name'];
                $photo->photoOrientation = $val['orient_portrait'];
                $photo->eventId = $val["event_id"];
                $output[] = $photo;
            }
            return $output;
        } else {
            return null;
        }
    }

    public function getPhotoById($photoId) {
        $this->updsql = "select * from photos where uid = $photoId";
        $res = $this->retrieve();
        if ($res != null) {
            $photoRec = $res[0];
            $photo = new Photo($photoRec['photo_name'], $photoRec['photo_uri'],
                            $photoRec['photo_price'], $photoRec['orient_portrait']);
            $photo->eventId = $photoRec['event_id'];
            $photo->photoThumbnail = $photoRec['photo_thumbnail_name'];
            $photo->photoId = $photoRec['uid'];
            return $photo;
        } else {
            return null;
        }
    }

}

