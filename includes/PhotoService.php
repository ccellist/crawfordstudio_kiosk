<?php

/**
 * Description of PhotoService
 *
 * @author arturo
 */
class PhotoService {

    private $photoDao;

    public function __construct() {
        $this->photoDao = new PhotoDao();
    }

    public function getPhotoById($photoId) {
        $photo = $this->photoDao->getPhotoById($photoId);
        return $photo;
    }

    public function getPhotoPriceById($photoId) {
        $photo = $this->getPhotoById($photoId);
        if ($photo != null) {
            return $photo->photoPrice;
        } else {
            return 0.00;
        }
    }

    public function getPhotoNameById($photoId) {
        $photo = $this->getPhotoById($photoId);
        if ($photo != null) {
            return $photo->photoName;
        } else {
            return "(invalid photo)";
        }
    }

    public function getPhotoUriById($photoId) {
        $photo = $this->getPhotoById($photoId);
        if ($photo != null) {
            return $photo->photoUri;
        } else {
            return "(invalid photo)";
        }
    }

    public function getPhotoOrientationById($photoId) {
        $photo = $this->getPhotoById($photoId);
        if ($photo != null) {
            return $photo->photoOrientation;
        } else {
            return Photo::LANDSCAPE;
        }
    }

    public function getPhotoMeetIdByPhotoId($photoId) {
        $photo = $this->photoDao->getPhotoById($photoId);
        if ($photo != null) {
            $meetEventId = $photo->eventId;
            $meetEventDao = new MeetEventDao();
            return $meetEventDao->getMeetIdFromMeetEventId($meetEventId);
        } else {
            return 0;
        }
    }

    public function getPhotoCountByMeetEventId($meetEventId) {
        $photos = $this->photoDao->getPhotosByMeetEventId($meetEventId);
        if ($photos != null) {
            return count($photos);
        } else {
            return 0;
        }
    }

    public function getPhotosByMeetEventId($meetEventId) {
        return $this->photoDao->getPhotosByMeetEventId($meetEventId);
    }

}

