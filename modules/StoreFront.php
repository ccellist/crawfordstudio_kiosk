<?php

/*
 * Created on Dec 30, 2011
 *
 * author: arturo
 * 
 */

class Mod_StoreFront extends AuthPublic implements Iface_StoreFrontModule {

    public function __construct($modName, $qry = "") {
        parent::__construct($modName, $qry);
    }

    public function _default() {
        $this->data = $this->qryString;
    }
    
    public function getSessions(){
        $meetId = $_POST['meetId'];
        $meetEventService = new MeetEventService();
        $sessionsDropdown = $meetEventService->generateMeetSessionDropdown($meetId);
        $this->data = $sessionsDropdown;
    }

    public function getEvents() {
        $meetId = $_POST['meetId'];
        $sessionId = $_POST['sessionId'];
        $meetEventService = new MeetEventService();
        $eventsDropdown = $meetEventService->generateMeetEventDropdown($meetId, $sessionId);
        $this->data = $eventsDropdown;
    }

    public function getRotations() {
        $eventId = $_POST['eventId'];
        $meetId = $_POST['meetId'];
        $sessionId = $_POST['sessionId'];
        $meetEventService = new MeetEventService();
        $rotationDropdown = $meetEventService->generateRotationDropdown($eventId, $sessionId, $meetId);
        $this->data = $rotationDropdown;
    }

    public function getPhotos() {
        $this->session = SessionTool::getSession();
        if (get_magic_quotes_runtime())
            set_magic_quotes_runtime(false);

        $eventId = $_POST['eventId'];
        if (is_array($this->qryString)) {
            $newPage = $this->qryString['p'];
        } elseif (strpos($this->qryString, "p") !== false) {
            $newPage = str_replace("p:", "", $this->qryString);
        } else {
            $newPage = 1;
        }

        $orderService = new OrderService();
        $photoService = new PhotoService();
        $allPhotos = $photoService->getPhotosByMeetEventId($eventId);
        if (PAGINATE_PHOTOS) {
            $pagination = PaginationFactory::getPagination($allPhotos, 1, PHOTOS_PER_PAGE);
            $photos = $pagination->goToPage($newPage);
            $pgNav = PaginationFactory::drawNav($pagination, $newPage, PHOTOS_PER_PAGE, $_SERVER['REQUEST_URI'], "ajax");
        } else {
            $photos = $allPhotos;
            $pgNav = "";
        }

        $count = 1;
        $this->data .= "<div id=\"thumbnailButtons\">\n";
        $this->data .= "<input type=\"button\" value=\"Clear\" onclick=\"clearItems()\" />\n";
        $this->data .= "<input type=\"checkbox\" id=\"chkBuyAll\" onclick=\"buyAll()\"> Select all photos on page\n";
        $this->data .= "$pgNav</div>\n";
        $this->data .= "<div id=\"thumbnailPanel\">\n<div class=\"thumbnailRow\">\n";
        if (is_array($photos)) {
            foreach ($photos as $photo) {
                $filename = str_replace($photo->photoUri . "/", "", $photo->photoThumbnail);
                $photoFilepath = $photo->photoUri . DIRECTORY_SEPARATOR . $photo->photoName;
                $thumbnailFilepath = $photo->photoUri . DIRECTORY_SEPARATOR . $photo->photoThumbnail;
                $checked = "";
                $width = "";
                $height = "";
                $rotateAngle = ImageExifProcessor::getPhotoRotateAngle($photoFilepath);
                if ($this->session->orderId != null) {
                    $order = $orderService->getOrderById($this->session->orderId);
                    if ($order != null) {
                        if ($orderService->isPhotoInOrder($order, $photo)) {
                            $checked = "checked ";
                        }
                    } else {
                        unset($this->session->orderId);
                    }
                }

                //Legacy code from when we weren't pulling photo orientation directly from exif tags
                //but from the database, which was an iffy method to begin with. Leaving here
                //just in case.
//                if ($photo->photoOrientation == Photo::PORTRAIT) {
//                    $rotateAngle = 90.0;
//                    $width = "width=\"100\"";
//                    $height = "height=\"140\"";
//                } else {
//                    $rotateAngle = 0.0;
//                    $width = "width=\"140\"";
//                    $height = "height=\"100\"";
//                }
                $this->data .= sprintf("<div class=\"thumbnailWrapper\" class=\"left\">");
                $this->data .= "<div class=\"thumbnailContainer left\">\n";
                $this->data .= sprintf("<div class=\"thumbnail\">\n<img onclick=\"showFullPhoto(%s, %s)\" class=\"thumbnail\" src=\"/includes/showPhoto.php?u=%s&r=%s\" %s %s></div><br>", $rotateAngle, $photo->photoId, urlencode($photo->photoUri . DIRECTORY_SEPARATOR . $photo->photoName), $rotateAngle, $width, $height);
                $this->data .= sprintf("<center><input type=\"checkbox\" value=\"%s\" onclick=\"managePhotoList(this.value)\" %s/>&nbsp;" . $photo->photoName . "</center>\n", $photo->photoId, $checked);
                $this->data .= "</div>\n";
                $this->data .= "</div>";

                if ($count % 3 == 0)
                    $this->data .="</div>\n<br>\n<div class=\"thumbnailRow\">\n";
                $count++;
            }
            $this->data .= "</div><br><br>\n<div style=\"clear:both\" />\n";
        } else {
            $this->data .= "No photos available.";
        }
        $this->data .= "</div></div>\n";
    }

    public function getPhotoId() {
        $photoService = new PhotoService();
        $photo = $photoService->getPhotoById($_POST['photoId']);
        $eventId = $photo->eventId;

        $photos = $photoService->getPhotosByMeetEventId($eventId);
        $photoKey = array_search($photo, $photos);
        $this->data = $photos[$photoKey + $_POST['interval']]->photoId;
    }

    public function getPhoto() {
        $width = "";
        $height = "";
        $rotateAngle = 0.0;
        $photoId = $this->qryString;
        if (strlen($photoId) > 0) {
            $photoService = new PhotoService();
            $photo = $photoService->getPhotoById($photoId);
            $rotateAngle = ImageExifProcessor::getPhotoRotateAngle($photo->photoUri . DIRECTORY_SEPARATOR . $photo->photoName);
            if ($rotateAngle == 90.0) {
                //$rotateAngle = 90.0;
                $width = "width=\"450\"";
                $height = "height=\"630\"";
            } else {
                //$rotateAngle = 0.0;
                $width = "width=\"630\"";
                $height = "height=\"450\"";
            }
            if ($this->isPhotoInOrder($this->session->orderId, $photoId)) {
                $cartButton = "<input id=\"popupPhotoAction\" type=\"button\" value=\"Remove from cart\" onclick=\"addSinglePhotoToCart(" . $photoId . ")\" />";
                $buttonDivWidth = "46%";
            } else {
                $cartButton = "<input id=\"popupPhotoAction\" type=\"button\" value=\"Add to cart\" onclick=\"addSinglePhotoToCart(" . $photoId . ")\" />";
                $buttonDivWidth = "36%";
            }
            $events["onclick"] = "showNextPhoto(-1)";
            $prevButton = new UI_Button("button", "&lt;", "prevPic", "", "navButton", $events);
            $events2["onclick"] = "showNextPhoto(1)";
            $nextButton = new UI_Button("button", "&gt;", "nextPic", "", "navButton", $events2);

            if ($this->isFirstPhotoOfMeet($photoId)) {
                $prevButton->disable();
            } elseif ($this->isLastPhotoOfMeet($photoId)) {
                $nextButton->disable();
            }
            $contents = "<div id=\"buttonDiv\" style=\"width:$buttonDivWidth\"><div class=\"left\">" .
                    $prevButton->getButtonHtml() . "</div><div class=\"left\">" .
                    $cartButton . "</div><div class=\"left\">" .
                    $nextButton->getButtonHtml() .
                    "</div><div style=\"clear:both\"></div></div>\n<div id=\"popupCartNotification\"></div>\n";
            $contents .= "<img id=\"imgDetail\" src=\"/includes/showPhoto.php?u=" . urlencode($photo->photoUri . DIRECTORY_SEPARATOR . $photo->photoThumbnail) . "&r=" . $rotateAngle . "&full=1\"" . $width . $height . ">";
            $this->data = $contents;
        } else {
            $events["onclick"] = "showNextPhoto(-1)";
            $prevButton = new UI_Button("button", "&lt;", "prevPic", "", "navButton", $events);
            $events2["onclick"] = "showNextPhoto(1)";
            $nextButton = new UI_Button("button", "&gt;", "nextPic", "", "navButton", $events2);
            $contents = "<div id=\"buttonDiv\">" . $prevButton->printButton() . $nextButton->printButton() . "</div>\n<div id=\"popupCartNotification\"></div>\n";
            $contents .= "Photo not found.";
            $this->data = $contents;
        }
    }

    private function isPhotoInOrder($orderId, $photoId) {
        if ($orderId != NULL) {
            $orderService = new OrderService();
            $order = $orderService->getOrderById($orderId);
            if ($order != null) {
                $photoService = new PhotoService();
                $photo = $photoService->getPhotoById($photoId);
                return $orderService->isPhotoInOrder($order, $photo);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function isLastPhotoOfMeet($photoId) {
        $photoPosition = $this->getPhotoPosition($photoId);
        $photoService = new PhotoService();
        $photo = $photoService->getPhotoById($photoId);
        $eventId = $photo->eventId;

        $photoCount = count($photoService->getPhotosByMeetEventId($eventId));
        if ($photoPosition >= $photoCount - 1) {
            return true;
        } else {
            return false;
        }
    }

    private function isFirstPhotoOfMeet($photoId) {
        $photoPosition = $this->getPhotoPosition($photoId);
        if ($photoPosition == 0) {
            return true;
        } else {
            return false;
        }
    }

    private function getPhotoPosition($photoId) {
        $photoService = new PhotoService();
        $photo = $photoService->getPhotoById($photoId);
        $eventId = $photo->eventId;

        $photos = $photoService->getPhotosByMeetEventId($eventId);
        $photoKey = array_search($photo, $photos);
        return $photoKey;
    }

}