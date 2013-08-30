<?php
/**
 * <DZCP-Extended Edition - Live! Server>
 * @package: DZCP-Extended Edition
 * @author: Hammermaps.de Developer Team
 * @link: http://www.hammermaps.de
 */

## Check IN_SYS ##
if (!defined('IN_SYS'))
    exit();

## SQL Config ##
$sql_config = array(
'system' => array('prefix' => '', 'host' => '127.0.0.1' ,'port' => '3306' ,'user' => 'root' ,'pass' => '' ,'db' => 'dzcp_live', 'debug' => false, 'sqltype' => 'mysqli', 'persistent' => true)
);
?>