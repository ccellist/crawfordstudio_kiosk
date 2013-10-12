<?php
/*
 * Created on Dec 30, 2011
 * 
 * This file is the main template for all html request
 * responses. Common html display code and formatting,
 * as well the master layout, if any, go here.
 *
 * author: Arturo Araya
 * 
 */
if (DEBUG_ON) {
    SessionTool::dumpSession();
}
$sidebarContents = array();
$gymMeetService = new GymMeetService();
$sidebarContents[] = $gymMeetService->generateMeetDropdown();
$sidebarContents[] = "<div id=\"sessionDropdown\"></div>\n";
$sidebarContents[] = "<div id=\"eventDropdown\"></div>\n";
$sidebarContents[] = "<div id=\"rotationList\"></div>\n";
$session = SessionTool::getSession();
$authLevel = $session->auth_level;
$photoList = "";
if (@$_GET['action'] != null) {
    $pageId = " id=\"" . $_GET['action'] . "_page\"";
} elseif (@$_GET['module'] == "StoreAdmin") {
    $pageId = " id=\"" . $_GET['module'] . "_page\"";
} else {
    $pageId = " id=\"default_page\"";
}
if ($session->orderId != null) {
    $orderId = $session->orderId;
    $orderService = new OrderService();
    $order = $orderService->getOrderById($orderId);
    if ($order != null) {
        $photoList = $orderService->getPhotoIdListForOrder($order);
    }
} else {
    $orderId = 0;
}

$events["onclick"] = "showNextPhoto(-1)";
$prevButton = new UI_Button("button", "&lt;", "prevPic", "", "navButton", $events);
$events2["onclick"] = "showNextPhoto(1)";
$nextButton = new UI_Button("button", "&gt;", "nextPic", "", "navButton", $events2);
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>Crawford Photo ordering console</title>
        <link type="text/css" href="includes/css/cupertino/jquery-ui-1.8.16.custom.css" rel="stylesheet" />	
        <script type="text/javascript" src="includes/javascript_src/jquery-1.6.2.min.js"></script>
        <script type="text/javascript" src="includes/javascript_src/jquery-ui-1.8.16.custom.min.js"></script>
        <script type="text/javascript" language="javascript" src="includes/javascript_src/mvc.js"></script>
        <script type="text/javascript" language="javascript">
            window.onload=function(){
                $("#pWest").height($("#primary").height() - 349);
                reloadOrderDetails();
                setInterval("requestReload()", <?php print DEFAULT_TIMEOUT; ?>);
            }
            
            var popupHeight = 22;
            var photos = "<?php print $photoList; ?>";
            var orderId = <?php print $orderId; ?>;
            var currPhotoId;
            var nextPhotoId;
            var meetEventId;
            
            function requestReload(){
                window.location='/index.php?module=User&action=logout';
            }
            
            function showNextPhoto(interval){
                $.ajax({
                    type: 'POST',
                    data: { photoId: currPhotoId, interval: interval },
                    url: "/index.php?module=StoreFront&action=getPhotoId&view=ajax",
                    beforeSend:function(){
                        // this is where we append a loading image
                    },
                    success:function(data){
                        // successful request; do something with the data
                        getPhoto(data);
                    },
                    error:function(){
                        // failed request; give feedback to user
                        return 0;
                    }
                });
            }
            
            function getPhoto(nextPhoto){
                $.ajax({
                    type: 'GET',
                    url: "/index.php?module=StoreFront&action=getPhoto&qry=" + nextPhoto + "&view=ajax",
                    beforeSend: function(){
                        // this is where we append a loading image
                        $('#popupContents').html('<div class="loading"><img src="/includes/images/loading.gif" alt="Loading..." /></div>');
                    },
                    success: function(data){
                        // successful request; do something with the data
                        $('#popupContents').empty();
                        $('#popupContents').append(data);
                        var imgIndex = data.toString().indexOf("img");
                        var widthIndex = data.toString().indexOf("width",imgIndex);
                        var widthEndIndex = data.toString().indexOf(" ", widthIndex);
                        var widthString = data.toString().substring(widthIndex, widthEndIndex);
                        var heightIndex = data.toString().indexOf("height");
                        var heightEndIndex = data.toString().indexOf(" ", heightIndex);
                        var heightString = data.toString().substring(heightIndex, heightEndIndex);
                        var widthArray = widthString.toString().split("=");
                        var heightArray = heightString.toString().split("=");
                        var newWidth = widthArray[1].replace(/\"/g, "");
                        var newHeight = heightArray[1].replace(/\"/g, "");
                        $('#popup').width(parseInt(newWidth) + 10);
                        $('#popup').height(parseInt(newHeight) + 80);
                        $('#popupHeader').width(parseInt(newWidth));
                        centerMe(document.getElementById('popup'), parseInt(newWidth) + 10, parseInt(newHeight) + 80, popupHeight,20);
                        if (nextPhoto.length > 0) currPhotoId = parseInt(nextPhoto);
                    },
                    error:function(){
                        // failed request; give feedback to user
                        $('#popupContents').html('<p class="error"><strong>Oops!</strong> Try that again in a few moments.</p>');
                    }
                });                
            }
            
            function addSinglePhotoToCart(photoId){
                managePhotoList(photoId);
                getThumbnails(meetEventId);
            }
            
            function buyAll(){
                var callback = function (){
                    $(this).attr("checked", "checked");
                    managePhotoList($(this).attr("value"));
                };
                $(':checkbox[id!="chkBuyAll"]').each(callback);
            }
            
            function managePhotoList(photoId){
                var photArr = photos.split(",");
                var found = false;
                for (var i=0;i<photArr.length;i++){
                    if (photArr[i] == photoId){
                        found = true;
                        break;
                    }
                }
                if (found){
                    delPhotoFromList(photoId);
                    $("#popupPhotoAction").attr("value", "Add to cart");
                    $("#buttonDiv").css("width", "36%");
                } else {
                    addPhotoToList(photoId);
                    $("#popupPhotoAction").attr("value", "Remove from cart");
                    $("#buttonDiv").css("width", "46%");
                }
                photos = photos.replace(/^\,/,"");
                submitPhotos()
            }
            
            function clearItems(){
                photos = "";
                $(':checkbox').attr("checked", false);
            }            
            
            function clearOrder(){
                $.ajax({
                    type: 'GET',
                    url: "/index.php?module=Order&action=clearAllItems&qry=" + orderId + "&view=ajax",
                    beforeSend:function(){
                        // this is where we append a loading image
                        $('#orderSummary').html('<div class="loading"><img src="/includes/images/loading.gif" alt="Loading..." /></div>');
                    },
                    success:function(data){
                        // successful request; do something with the data                        
                        reloadOrderDetails();
                        $('#cartSummary').empty();
                        $('#cartSummary').append(data);
                        if (meetEventId > 0) getThumbnails(meetEventId);
                        photos = "";
                    },
                    error:function(){
                        // failed request; give feedback to user
                        $('#orderSummary').html('<p class="error"><strong>Oops!</strong> Try that again in a few moments.</p>');
                    }
                });
            }
            
            function addPhotoToList(photoId){
                photos = photos + "," + photoId;
            }
            
            function delPhotoFromList(photoId){
                var photArr = photos.split(",");
                var i;
                for (i=0;i<photArr.length;i++){
                    if (photArr[i] == photoId){
                        break;
                    }
                }
                photArr.splice(i,1);
                photos = photArr.join(",");
                photos = photos.replace(",,",",");
            }
            
            function getMeetSessions(meet){
                $.ajax({
                    type: 'POST',
                    url: '/index.php?module=StoreFront&action=getSessions&view=ajax',
                    data: { meetId: meet },
                    beforeSend:function(){
                        // this is where we append a loading image
                        $('#ajax-panel').html('<div class="loading"><img src="/includes/images/loading.gif" alt="Loading..." /></div>');
                    },
                    success:function(data){
                        // successful request; do something with the data
                        $('#mainContents').empty();
                        $('#mainContents').append("Select an event.");
                        $('#sessionDropdown').empty();
                        $('#sessionDropdown').append(data);
                        var newHeight = $('#sessionDropdown').height();
                        $('#sidebar').height(newHeight);
                        $('#rotationList').empty();
                    },
                    error:function(){
                        // failed request; give feedback to user
                        $('#sessionDropdown').html('<p class="error"><strong>Oops!</strong> Try that again in a few moments.</p>');
                    }
                });
            }
            
            function getMeetEvents(session, meet){
                $.ajax({
                    type: 'POST',
                    url: '/index.php?module=StoreFront&action=getEvents&view=ajax',
                    data: { meetId: meet, sessionId: session },
                    beforeSend:function(){
                        // this is where we append a loading image
                        $('#ajax-panel').html('<div class="loading"><img src="/includes/images/loading.gif" alt="Loading..." /></div>');
                    },
                    success:function(data){
                        // successful request; do something with the data
                        $('#mainContents').empty();
                        $('#mainContents').append("Select an event.");
                        $('#eventDropdown').empty();
                        $('#eventDropdown').append(data);
                        var newHeight = $('#eventDropdown').height();
                        $('#sidebar').height(newHeight);
                        $('#rotationList').empty();
                    },
                    error:function(){
                        // failed request; give feedback to user
                        $('#eventDropdown').html('<p class="error"><strong>Oops!</strong> Try that again in a few moments.</p>');
                    }
                });
            }
            
            function getRotations(eventId, meetId, sessionId){
                $.ajax({
                    type: 'POST',
                    url: '/index.php?module=StoreFront&action=getRotations&view=ajax',
                    data: { eventId: eventId, meetId: meetId, sessionId: sessionId },
                    beforeSend:function(){
                        // this is where we append a loading image
                        $('#ajax-panel').html('<div class="loading"><img src="/includes/images/loading.gif" alt="Loading..." /></div>');
                    },
                    success:function(data){
                        // successful request; do something with the data
                        $('#mainContents').empty();
                        $('#mainContents').append("Select a rotation.");
                        $('#rotationList').empty();
                        $('#rotationList').append(data);
                        var newHeight = $('#rotationList').height();
                        $('#sidebar').height(newHeight);
                    },
                    error:function(){
                        // failed request; give feedback to user
                        $('#eventDropdown').html('<p class="error"><strong>Oops!</strong> Try that again in a few moments.</p>');
                    }
                });
            }
            
            function getThumbnails(event){
                meetEventId = event;
                $.ajax({
                    type: 'POST',
                    url: '/index.php?module=StoreFront&action=getPhotos&view=ajax',
                    data: { eventId: event },
                    beforeSend:function(){
                        // this is where we append a loading image
                        $('#mainContents').html('<div class="loading"><img src="/includes/images/loading.gif" alt="Loading..." /></div>');
                    },
                    success:function(data){
                        // successful request; do something with the data
                        $('#mainContents').empty();
                        $('#mainContents').append(data);
                        var newHeight = $('#mainContents').height();
                        $('#sidebar').height(newHeight);
                    },
                    error:function(){
                        // failed request; give feedback to user
                        $('#mainContents').html('<p class="error"><strong>Oops!</strong> Try that again in a few moments.</p>');
                    }
                });
            }
            
            function nextPage(url){
                var url = decodeURI(url);
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: { eventId: meetEventId },
                    beforeSend:function(){
                        // this is where we append a loading image
                        $('#mainContents').html('<div class="loading"><img src="/includes/images/loading.gif" alt="Loading..." /></div>');
                    },
                    success:function(data){
                        // successful request; do something with the data
                        $('#mainContents').empty();
                        $('#mainContents').append(data);
                        var newHeight = $('#mainContents').height();
                        $('#sidebar').height(newHeight);
                    },
                    error:function(){
                        // failed request; give feedback to user
                        $('#mainContents').html('<p class="error"><strong>Oops!</strong> Try that again in a few moments.</p>');
                    }
                });
            }
            
            function submitPhotos(){
                $.ajax({
                    type: 'POST',
                    url: '/index.php?module=Order&action=modifyCart&view=ajax',
                    data: { photoIds: photos },
                    beforeSend:function(){
                        // this is where we append a loading image
                        $('#ajax-panel').html('<div class="loading"><img src="/includes/images/loading.gif" alt="Loading..." /></div>');
                    },
                    success:function(data){
                        // successful request; do something with the data
                        orderId = data;
                        reloadOrderDetails();
                        if($('#popup').css("visibility") == "visible"){
                            $("#popupCartNotification").html("<span id=\"notificationText\">Cart updated</span>");
                            $("#popupCartNotification")
                            .show(700)
                            .delay(800)
                            .hide(700);
                        }
                    },
                    error:function(){
                        // failed request; give feedback to user
                        $('#mainContents').html('<p class="error"><strong>Oops!</strong> Try that again in a few moments.</p>');
                    }
                });
            }
            
            function reloadOrderDetails(){
                $.ajax({
                    type: 'GET',
                    url: "/index.php?module=Order&action=showOrderSummary&qry=" + orderId + "&view=ajax",
                    beforeSend:function(){
                        // this is where we append a loading image
                        $('#orderSummary').html('<div class="loading"><img src="/includes/images/loading.gif" alt="Loading..." /></div>');
                    },
                    success:function(data){
                        // successful request; do something with the data
                        $('#orderSummary').empty();
                        $('#orderSummary').append(data);
                    },
                    error:function(){
                        // failed request; give feedback to user
                        $('#orderSummary').html('<p class="error"><strong>Oops!</strong> Try that again in a few moments.</p>');
                    }
                });
            }
            
            function showFullPhoto(rotate, photoId){
                currPhotoId = photoId;
                if (rotate == 90.0){
                    width = 450;
                    height = 630;
                } else {
                    width = 630;
                    height = 450;
                }
                contents = "";
                getPhoto(photoId);
                showPopup("Photo detail", contents, width+10, height+80, popupHeight);
            }
        </script>
        <link rel=stylesheet type="text/css" href="includes/css/master.css">

    </head>
    <body<?php print $pageId; ?> >
        <div class="popup" id="popup"></div>
        <div id="container">
            <div id="primary">
                <div id="pNorth">
                    <?php
                    UI_UiHelper::drawBanner(IMG_DIR . "/blog-banner.jpg");
                    if ($authLevel == "admin") {
                        UI_UiHelper::drawStoreHNav(false, true);
                    } else {
                        UI_UiHelper::drawStoreHNav();
                    }
                    ?> 
                </div>
                <div style="clear:both"></div>
                <div id="pEquat">
                    <div id="pWest">
                        <?php UI_UiHelper::drawStoreSidebar($sidebarContents); ?>
                    </div><!-- div#pWest -->	
                    <div id='main_panel'>
                        <div id='mp_contents'>
                            <!--Main content begin-->
                            <div id='errors'>
                                <?php print UI_UiHelper::translateError($pgErr) ?>
                            </div>
                            <?php print $mainContents; ?>
                            <!--Main content end-->
                        </div><!--div#mp_contents-->
                    </div><!--div#main_panel-->
                    <div id="pEast">
                        <div id="orderSummary"></div>
                    </div><!--div#pEast-->
                </div><!-- div#pEquat -->
                <div style="clear:both"></div>
            </div><!--div#primary-->
        </div><!--div#container-->
    </body>
</html>
