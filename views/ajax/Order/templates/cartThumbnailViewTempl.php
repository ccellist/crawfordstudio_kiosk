<?php
$order = $dispData;
$orderItems = $order->orderItems;
$thumbnailView = "";

$events["onclick"] = "showNextPhoto(-1)";
$prevButton = new UI_Button("button", "&lt;", "prevPic", "", "navButton", $events);
$events2["onclick"] = "showNextPhoto(1)";
$nextButton = new UI_Button("button", "&gt;", "nextPic", "", "navButton", $events2);

$count = 1;
if (is_array($orderItems)) {
    foreach ($orderItems as $orderItem) {
        $photo = $orderItem->photo;
        $checked = "";

        $filename = str_replace($photo->photoUri . "/", "", $photo->photoName);
        $photoFilepath = $photo->photoUri . DIRECTORY_SEPARATOR . $photo->photoName;
        $width = "";
        $height = "";
        $rotateAngle = ImageExifProcessor::getPhotoRotateAngle($photoFilepath);
//        if ($photo->photoOrientation == Photo::PORTRAIT) {
//            $rotateAngle = 90.0;
//            $width = 100;
//            $height = 140;
//        } else {
//            $rotateAngle = 0.0;
//            $width = 140;
//            $height = 100;
//        }
        $thumbnailView .= sprintf("<div id=\"thumbnail\" class=\"left\">");
        $thumbnailView .= sprintf("<div id=\"photoDiv_%s\" class=\"left\"><img onclick=\"showFullPictureNoNav('%s',%s, %s)\" class=\"thumbnail\" src=\"includes/showPhoto.php?u=%s&r=%s\" width=\"%s\" height=\"%s\"><br>", $photo->photoId, urlencode($photo->photoUri . DIRECTORY_SEPARATOR . $photo->photoName), $rotateAngle, $photo->photoId, urlencode($photo->photoUri . DIRECTORY_SEPARATOR . $photo->photoName), $rotateAngle, $width, $height);
        $thumbnailView .= sprintf("<center><input type=\"checkbox\" id=\"%s\" />&nbsp;%s</center></div>\n", $photo->photoId, $photo->photoName);
        $thumbnailView .= "</div></div>";

        if ($count % 3 == 0)
            $thumbnailView .="<br>\n";
        $count++;
    }
    $thumbnailView .= "<br><br>";
} else {
    $thumbnailView = "No photos available.";
}
?>
<script type="text/javascript">
    function removeItems(){
        var items = "";
        $("input:checked").each(function(){
            items += this.id + " ";
        });
        var tmp = items.toString().trim().split(" ");
        items = Array(tmp).join(",");
        $.ajax({
            type: 'POST',
            data: { items: items, orderId: "<?php print $order->orderId; ?>" },
            url: "/index.php?module=Order&action=removeSelectedItems&view=ajax",
            beforeSend:function(){
                // this is where we append a loading image
                $('#orderSummary').html('<div class="loading"><img src="/includes/images/loading.gif" alt="Loading..." /></div>');
            },
            success:function(data){
                // successful request; do something with the data                        
                reloadOrderDetails();
                $("input:checked").each(function(){
                    var id = this.id;
                    $("#orderRow_" + id).hide();
                    $("#orderRow_" + id).empty();
                    $("#photoDiv_" + id).empty();
                });
            },
            error:function(){
                // failed request; give feedback to user
                $('#orderSummary').html('<p class="error"><strong>Oops!</strong> Try that again in a few moments.</p>');
            }
        });
    }
    /*function clearOrder(){
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
            },
            error:function(){
                // failed request; give feedback to user
                $('#orderSummary').html('<p class="error"><strong>Oops!</strong> Try that again in a few moments.</p>');
            }
        });
    }*/
    function showSummaryView(){
        $.ajax({
            type: 'GET',
            url: "/index.php?module=Order&action=showCart&qry=" + orderId + "&view=ajax",
            beforeSend:function(){
                // this is where we append a loading image
                $('#orderSummary').html('<div class="loading"><img src="/includes/images/loading.gif" alt="Loading..." /></div>');
            },
            success:function(data){
                // successful request; do something with the data                        
                reloadOrderDetails();
                $('#mp_contents').empty();
                $('#mp_contents').append(data);
            },
            error:function(){
                // failed request; give feedback to user
                $('#orderSummary').html('<p class="error"><strong>Oops!</strong> Try that again in a few moments.</p>');
            }
        });
    }
            
    function showFullPictureNoNav(uri, rotate, photoId){
        currPhotoId = photoId;
        if (rotate == 90.0){
            width = 450;
            height = 630;
        } else {
            width = 630;
            height = 450;
        }
        //var prevButton = "<?php print preg_replace("/\n/", "", addslashes($prevButton->getButtonHtml())); ?>";
        // var nextButton = "<?php print preg_replace("/\n/", "", addslashes($nextButton->getButtonHtml())); ?>";
        //var contents = "<div id=\"buttonDiv\">" + prevButton.replace(/\n/,"") + nextButton.replace(/\n/, "") + "</div>\n<div id=\"popupCartNotification\"></div>\n";
        var contents = "<div id=\"popupCartNotification\"></div>\n";
        contents += "<img id=\"imgDetail\" src=\"includes/showPhoto.php?u=" + uri + "&r=" + rotate + "&full=1\" width=\"" + width + "\" height=\"" + height + "\">"
        showPopup("Photo detail", contents, width+10, height+80, popupHeight);
    }
</script>
<?php print $thumbnailView; ?>