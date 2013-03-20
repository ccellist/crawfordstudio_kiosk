<?php

define("BASE_PATH", dirname(__FILE__));
require_once(BASE_PATH . "/includes/config.php");

function __autoload($class) {
    if (preg_match("/^Mod_/", $class)) {
        $file = preg_replace("/^Mod_/", "", $class) . ".php";
        require_once(BASE_PATH . "/modules/" . $file);
    } elseif (preg_match("/^UI_/", $class)) {
        $file = preg_replace("/^UI_/", "", $class) . ".php";
        require_once(BASE_PATH . "/includes/UI/" . $file);
    } elseif (preg_match("/Exception$/", $class)) {
        require_once(BASE_PATH . "/includes/Exceptions/" . $class . ".php");
    } else {
        $file = str_replace("_", "/", preg_replace("/s$/", "", $class)) . ".php";
        $file = preg_replace("/Iface/", "interfaces", $file);
        require_once(BASE_PATH . "/includes/" . $file);
    }
}

if (isset($_GET['module'])) {
    $module = $_GET['module'];
} else {
    $module = DEFAULT_MODULE;
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $viewaction = $action;
} else {
    $action = "_default";
    $viewaction = "default";
}
/* Clean and validate all user input from forms */
$_GET = InputValidator::validate($_GET);
if (count($_POST) > 0) {
    $_POST = InputValidator::validate($_POST);
}
/* -------------------------------------------- */

if (isset($_GET['qry'])) {
    $queryString = $_GET['qry'];
} else {
    $queryString = "";
}

if (isset($_GET['e'])) {
    $errCode = $_GET['e'];
} else {
    $errCode = 0;
}

$moduleName = preg_replace("/s$/", "", $module);
if (isset($_GET['view'])) {
    $view = $_GET['view'] . "_$moduleName" . "_" . $viewaction;
} else {
    $view = "html_$moduleName" . "_$viewaction";
}

$class = "Mod_" . preg_replace("/s$/", "", $module);
$modFileName = BASE_PATH . "/modules/$moduleName.php";

if (file_exists($modFileName)) {
    include($modFileName);
    $modInstance = new $class($moduleName, $queryString);
    try {
        if (!$modInstance->authenticate()) {
            $authClass = $modInstance->authClassName;
            throw new LoginException($authClass, "Login required.", LOGIN_REQUIRED);
        }
        if (method_exists($class, $action)) {
            $modInstance->actionName = $action;
            $modInstance->$action();
        } else {
            throw new Exception("Invalid action '$action'. Line:" . __LINE__);
        }
        $presType = substr($view, 0, strpos($view, "_"));
        $viewFile = preg_replace("/^" . $presType . "_" . $moduleName . "_/", "", $view);
        $viewFileName = BASE_PATH . "/views/$presType/$moduleName/$viewFile.php";
        if (file_exists($viewFileName)) {
            include($viewFileName);
            $template = new $view($modInstance->getData(), $moduleName, $errCode);
            if ($template instanceof $view) {
                $template->display();
            } else {
                throw new Exception("Invalid view selected. '$template' not an instance of '$view'. Line:" . __LINE__);
            }
        } else {
            throw new Exception("Invalid view selected. File not found: '$viewFileName' Line:" . __LINE__);
        }
    } catch (Exception $e) {
        if ($e instanceof LoginException) {
            $userModule = preg_replace("/Auth/", "", $e->getLoginType());
            $destUrl = urlencode($_SERVER['REQUEST_URI']);
            header("Location: /index.php?module=$userModule&action=login&qry=$destUrl");
        } else {
            $msg = urlencode("Error displaying module '$module':" . $e->getMessage());
            header("Location: /index.php?module=Error&qry=$msg");
            //die("Error displaying module '$module':\n". $e->getMessage()."\n");
        }
    }
} else {
    die("Invalid module '$module'. File not found: '$viewFileName' Line:" . __LINE__);
}

/* UNCOMMENT CODE, FOR USE IN WINDOWS PLATFORMS ONLY
That it is an implementation of the function money_format for the 
platforms that do not it bear.  

The function accepts to same string of format accepts for the 
original function of the PHP.  

The function is tested using PHP 5.1.4 in Windows XP 
and Apache WebServer. 

function money_format($format, $number) 
{ 
    $regex  = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?'. 
              '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/'; 
    if (setlocale(LC_MONETARY, 0) == 'C') { 
        setlocale(LC_MONETARY, ''); 
    } 
    $locale = localeconv(); 
    preg_match_all($regex, $format, $matches, PREG_SET_ORDER); 
    foreach ($matches as $fmatch) { 
        $value = floatval($number); 
        $flags = array( 
            'fillchar'  => preg_match('/\=(.)/', $fmatch[1], $match) ? 
                           $match[1] : ' ', 
            'nogroup'   => preg_match('/\^/', $fmatch[1]) > 0, 
            'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ? 
                           $match[0] : '+', 
            'nosimbol'  => preg_match('/\!/', $fmatch[1]) > 0, 
            'isleft'    => preg_match('/\-/', $fmatch[1]) > 0 
        ); 
        $width      = trim($fmatch[2]) ? (int)$fmatch[2] : 0; 
        $left       = trim($fmatch[3]) ? (int)$fmatch[3] : 0; 
        $right      = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits']; 
        $conversion = $fmatch[5]; 

        $positive = true; 
        if ($value < 0) { 
            $positive = false; 
            $value  *= -1; 
        } 
        $letter = $positive ? 'p' : 'n'; 

        $prefix = $suffix = $cprefix = $csuffix = $signal = ''; 

        $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign']; 
        switch (true) { 
            case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+': 
                $prefix = $signal; 
                break; 
            case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+': 
                $suffix = $signal; 
                break; 
            case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+': 
                $cprefix = $signal; 
                break; 
            case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+': 
                $csuffix = $signal; 
                break; 
            case $flags['usesignal'] == '(': 
            case $locale["{$letter}_sign_posn"] == 0: 
                $prefix = '('; 
                $suffix = ')'; 
                break; 
        } 
        if (!$flags['nosimbol']) { 
            $currency = $cprefix . 
                        ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) . 
                        $csuffix; 
        } else { 
            $currency = ''; 
        } 
        $space  = $locale["{$letter}_sep_by_space"] ? ' ' : ''; 

        $value = number_format($value, $right, $locale['mon_decimal_point'], 
                 $flags['nogroup'] ? '' : $locale['mon_thousands_sep']); 
        $value = @explode($locale['mon_decimal_point'], $value); 

        $n = strlen($prefix) + strlen($currency) + strlen($value[0]); 
        if ($left > 0 && $left > $n) { 
            $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0]; 
        } 
        $value = implode($locale['mon_decimal_point'], $value); 
        if ($locale["{$letter}_cs_precedes"]) { 
            $value = $prefix . $currency . $space . $value . $suffix; 
        } else { 
            $value = $prefix . $value . $space . $currency . $suffix; 
        } 
        if ($width > 0) { 
            $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ? 
                     STR_PAD_RIGHT : STR_PAD_LEFT); 
        } 

        $format = str_replace($fmatch[0], $value, $format); 
    } 
    return $format; 
}  */

function get_ip_list() {
    $tmp = array();
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
        $tmp += explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $tmp[] = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    $tmp[] = $_SERVER['REMOTE_ADDR'];
    return $tmp;
}
