<div id="item_detail">
    <?php
    print "<h3>$name</h3>\n";
    print "<div id='price'><h4>$unitcost</h4></div>\n";
    print "<div id='big_img'>\n";
    print "<img src='$imgSrc'>\n";
    print "</div> <!-- div#big_img -->\n";
    print "<div id='img_catwalk'>\n";
    print "</div> <!-- div#img_catwalk -->\n";
    ?>
    <script type="text/javascript" language="javascript">
        <!--
        function nonAjaxUpdateCart(itmid)
        {
            window.location="http://store.jennjilldesigns.com/ajax_scripts/additemtocart.php?uid=" + itmid;
        }

        function updateCart(sender,item_id)
        {
            $(sender).val("Adding...");
            $.post(
            "/index.php?module=Cart&action=addToCart&view=ajax",
            {
                uid: item_id
            },
            function(data, textStatus)
            {
                $(sender).val("Add to cart");
                if (!isNaN(data)) {
                    $("#cart_item_qty").text(data);
                } else {
                    showPopup("Error:",data, 200, 50);
                }
		
            });
        }
        -->
    </script>

    <?php
//print "<div id='addToCart'>\n<input type='button' value='Add to cart' onclick='javascript:updateCart(this, $item_id)'/>\n</div>\n";
    UI_JJDHelper::drawButton("button", "Add to cart", "addToCart", "", "", array("onclick" => "javascript:updateCart(this, $item_id)"), array());

    $session = SessionTool::getSession();
    if ($session->auth_level == "admin") {
        UI_JJDHelper::drawButton("button","Edit item", "editItem", "", "", array("onclick" => sprintf("window.location='/index.php?module=StoreAdmin&" .
                "action=editItem&qry=%s'", $item_id)));
        $adminButton = sprintf("<input type=\"button\" value=\"edit\" " .
                "onclick=\"window.location='/index.php?module=StoreAdmin&" .
                "action=editItem&qry=%s'\">", $item_id);
    } else {
        $adminButton = "";
    }
    print "<div id='itm_desc'>\n<p>";
    print $description . "\n";
    print "</p>\n</div><!--div#itm_desc-->\n";
    ?>

</div><!--div#item_detail-->

