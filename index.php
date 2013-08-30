<?php
/**
 * <DZCP-Extended Edition - Live! Server>
 * @package: DZCP-Extended Edition
 * @author: Hammermaps.de Developer Team
 * @link: http://www.hammermaps.de
 */

## Set Json Header ##
if(!isset($_REQUEST['dev']))
{
    ini_set('display_errors', 0);
    error_reporting(0);
    header('Content-type: text/plain');
}
else
{
    ini_set('display_errors', 1);
    error_reporting(E_ALL & ~E_NOTICE);
    echo "<pre>";
}

if(!isset($_POST['input']) || empty($_POST['input']))
    die('DZCP-EE API Server 01');

## ROOT_PATH ##
define('ROOT_PATH', str_replace('\\','/',realpath(dirname(__FILE__) . '/') . '\\'));
define('IN_SYS', true);

## Include Core ##
require(ROOT_PATH . 'inc/common.php');

///////////////////// MAIN //////////////////////////////

module::init();
server_logic::caller();

## End Buffer *Flush ##
ob_end_flush();