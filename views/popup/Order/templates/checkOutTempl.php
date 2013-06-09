<?php
$result = $dispData['resultData'];
$foundCustomer = $dispData['foundCustomer'];

if ($foundCustomer) {
    ?>
    <body onload="setTimeout(function(){parent.parent.window.location='/index.php?module=User&action=logout';}, <?php print CHECKOUT_PAGE_TIMEOUT; ?>)">
        <div id="checkoutFormContainer"><?php print $result; ?><br>
            <input type="button" value="Continue shopping" onclick="parent.parent.window.location='/index.php'"/>
        </div>
    </body>
    <?php
} else {
    ?>
    <body>
        <script type="text/javascript">
            function validateContact(){
                var email = $("#email").attr("value");
                var phone = $("#phone").attr("value");
                                            
                if ((email.toString().length == 0) && (phone.toString().length == 0)){                
                    $("#errMsg").html("Please enter a valid contact information, either an email address or phone number.");
                    $("#email").css("background-color","yellow");
                    $("#phone").css("background-color", "yellow");
                } else {
                    $("#checkout").submit();
                }
            }
        </script>
        <div id="mainContents">
            <div id="checkoutFormContainer">
                <span id="errMsg" style="color:red; font-weight: 400"></span>
                <form id="checkout" action="<?php print $result; ?>" method="POST">
                    <div class="label left">First name:</div> <div class="left"><input type="text" name="firstName" id="firstName" /></div><br>
                    <div class="label left">Last name:</div> <div class="left"><input type="text" name="lastName" id="lastName" /></div><br>
                    <div class="label left">Email address:</div> <div class="left"><input type="text" name="email" id="email"/></div><br>
                    <div class="label left">Primary phone number:</div> <div class="left"><input type="text" name="phone" id="phone" /></div><br>
                    <div style="clear:both"></div>
                    <div id="buttonDiv">
                        <input type="button" value ="Submit" id="submitButton" onclick="validateContact()"/><input type="reset" value="clear"/>
                    </div>
                </form>
            </div>
        </div>
    </body>
    <?php
}
?>