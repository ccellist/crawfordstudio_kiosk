<?php
setlocale(LC_MONETARY, 'en_US');
date_default_timezone_set("America/New_York");
define("BULK_EMAIL_RETURN_ADDRESS","no-reply@<domain.com>");
define("ADMIN_HOME_PG","/index.php?module=UserMgmt");
define("USER_HOME_PG","/index.php?module=Member");
define("WEBSITE_NAME","Crawford Studio kiosk");
define("IMG_DIR","/includes/images");
define("MEMCACHED_ENABLED", true);
define("LOCALITY_TAX_RATE", 0.05);
define("COPY_COMMAND", "cp");
//define("IMAGE_PROCESSOR", "GD2");
//define("IMAGE_PROCESSOR", "ImageMagick");
define("IMAGE_PROCESSOR", "Exif");
define("PHOTOS_PER_PAGE", 15);
define("PAGINATE_PHOTOS", false);
define("ALLOW_DUPLICATE_LANDSCAPE_PHOTOS", false);
define("ORIENTATION", "orientation");
define("CLASSIC", "classic");
define("PAGINATION_STYLE", ORIENTATION);
define("PHOTO_DESTINATION", "c:\\users\\aa94427\\Desktop\\Orders");
define("DEFAULT_TIMEOUT", "300000");
define("CHECKOUT_PAGE_TIMEOUT", 800);
define("STOP_PROCESSING", 0.0);
define("LOG_DIR", "/var/log/apache2/appLogs");
define("QUERY_LOG_FILE", "crawfordQry.log");

define("MY_SERVER","127.0.0.1");
define("MY_DB","crawfordphoto");
define("MY_USER","APP");
define("MY_PASSWORD","APP");
define("SESSIONS_DB_SERVER","127.0.0.1");
define("SESSIONS_DB","admin");
define("SESSIONS_USER","APP");
define("SESSIONS_PASSWORD","APP");
define("SESSIONS_TABLE","sessiondata");

define("APP_COOKIE_TIMEOUT", 30*24*60*60);
define("USER_LOGIN_EXP_TIME", 60*60);
define("LOGIN_REQUIRED",500);
define("INVALID_LOGIN",501);
define("DEFAULT_MODULE", "StoreFront");

define("APP_HAS_CART", false);
define("HOME_SPLASH","www.crawfordstudio.com");
define("BLOG_URL","www.crawfordstudio.com/blog");
define("DOMAIN_NAME","kiosk.crawfordstudio.com");
define("INDEX_URL","http://".DOMAIN_NAME."/index.php");
define("SQL_NOW",date("Y/m/d H:i:s", time()));
define("HALF_HOUR",60 /*seconds*/ * 30 /*minutes*/);
define("HOUR",60 /*seconds*/ * 60 /*minutes*/);
define("DAY",60 /*seconds*/ * 60 /*minutes*/ * 24 /*hours*/);
define("THIRTY_DAYS",60 /*seconds*/ * 60 /*minutes*/ * 24 /*hours*/ * 30 /*days*/);
define("HALF_YEAR",60 /*seconds*/ * 60 /*minutes*/ * 24 /*hours*/ * (365/2) /*days*/);
define("ONE_YEAR",60 /*seconds*/ * 60 /*minutes*/ * 24 /*hours*/ * 365 /*days*/);
define("UPDATE_FAILED", 502);
?>
