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

## End PageTimer ##
$time_end = getmicrotime();
$time = $time_end - $time_start;

///////////////////// MAIN //////////////////////////////

server_logic::set_api_cryptkey('test1234');

function module($data='')
{
    die(server_logic::get_api_ident());
    if(server_logic::get_api_ident())
    switch ($data['call'])
    {
        case 'news':
            return array('sdffdsfds' => 'fgdgffg');
        break;

        default:
        break;
    }


    print_r($data);
    die();

    return array('fdsfsdsdffds' => 'fgdgffg');
}

server_logic::caller();

## End Buffer *Flush ##
ob_end_flush();