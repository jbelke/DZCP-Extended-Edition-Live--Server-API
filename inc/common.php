<?php
/**
 * <DZCP-Extended Edition - Live! Server>
 * @package: DZCP-Extended Edition
 * @author: Hammermaps.de Developer Team
 * @link: http://www.hammermaps.de
 */

## Check IN_SYS ##
if (!defined('IN_SYS')) exit();

## Sessions ##
if(!headers_sent())
    @session_start();

## Start Timer ##
function getmicrotime()
{
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}

$time_start = getmicrotime();

## Include Core Files ##
require(ROOT_PATH . 'inc/sql.php'); ## CMS SQL Config ##
require(ROOT_PATH . 'inc/secure.php'); ## Anti-Injection Protect ##
require(ROOT_PATH . 'inc/kernel.php'); ## CMS Kernel Funktions ##
require(ROOT_PATH . 'inc/DBQ/class.db.manager.php'); ## CMS DBQ *Datenbank MGR ##

###################
## Initialisiere ##
###################

## Include Kernel Classes ##
if(($files = kernel::list_background_files(ROOT_PATH . 'inc/class/',false,true,array("php"))))
{
    foreach($files as $func)
    {
        if(!include_once(ROOT_PATH.'inc/class/'.$func))
            die('CMSKernel: Can not include "/inc/class/'.$func.'"!');
    }
}
unset($files);