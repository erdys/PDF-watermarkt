<?php

session_start();
define('PREFIX_TRACKING',"a");
define('TRACKING_DEFAULT',"WEB/17");
$tracking='';

/**
 * clear text
 * @param unknown $v
 * @return string
 */

function clearTxt($v){
    $v = strip_tags($v);
    $v = htmlentities($v);
    return $v;
}


if (isset($_GET[PREFIX_TRACKING]) and mb_strlen($_GET[PREFIX_TRACKING])>1 ){
    $tracking=clearTxt($_GET[PREFIX_TRACKING]);
}


if ($tracking==''){
    if (isset($_SESSION[PREFIX_TRACKING]) and mb_strlen($_SESSION[PREFIX_TRACKING])>1 ){
        $tracking=$_SESSION[PREFIX_TRACKING];
    }
}


if ($tracking==''){
    if (isset($_COOKIE[PREFIX_TRACKING]) and mb_strlen($_COOKIE[PREFIX_TRACKING])>1 ){
        $tracking=clearTxt($_COOKIE[PREFIX_TRACKING]);
    }
}

if ($tracking==''){
    $tracking=TRACKING_DEFAULT;
}
setcookie(PREFIX_TRACKING,$tracking,time() + (86400* 7));