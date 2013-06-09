<html>
    <head>
        <title>Crawford Photo ordering console</title>
        <link type="text/css" href="includes/css/cupertino/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
        <link type="text/css" href="includes/css/master.css" rel="stylesheet" />
        <script type="text/javascript" src="includes/javascript_src/jquery-1.6.2.min.js"></script>
        <script type="text/javascript" src="includes/javascript_src/jquery-ui-1.8.16.custom.min.js"></script>
        <script type="text/javascript" language="javascript" src="includes/javascript_src/mvc.js"></script>
        <script type="text/javascript" language="javascript">
            window.onload=function(){
                $("#pWest").height($("#primary").height() - 349);
            }
        </script>
    </head>
    <?php
    print $mainContents;
    ?>
</html>