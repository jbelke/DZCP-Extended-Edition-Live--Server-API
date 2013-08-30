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

class module
{
    private static $module_index = array();

    public static function init()
    {
        //Index Modules
        $modules = kernel::list_background_files(ROOT_PATH . 'modules/',true);
        if($modules && count($modules) >= 1)
        {
            foreach($modules as $module)
            {
                if(file_exists(ROOT_PATH . 'modules/'.$module.'/module_info.xml'))
                {
                    $moduleName = 'module_'.$module; $info_array = array();
                    if(xml::openXMLfile($moduleName, 'modules/'.$module.'/module_info.xml',true))
                    {
                        $additional_functions = kernel::list_background_files(ROOT_PATH . 'modules/'.$module.'/functions/',false,true,array('php'));
                        $additional_indexes = kernel::list_background_files(ROOT_PATH . 'modules/'.$module.'/',false,true,array('php'));
                        $xml = xml::getXMLvalue($moduleName,'/info');
                        $info_array['xml_module_obj'] = $xml;
                        $info_array['xml_module_path'] = 'modules/'.$module.'/';
                        $info_array['xml_module_functions'] = $additional_functions;
                        $info_array['xml_module_indexes'] = $additional_indexes;
                        $info_array['xml_call_function'] = convert::ToString($xml->module_call_function);
                    }

                    self::$module_index[convert::ToString($xml->module_on_call)] = $info_array;
                    unset($xml,$info_array,$additional_functions,$additional_indexes);
                }
                else
                    continue;
            }
        }
    }

    public static function include_module($call='')
    {
        if(array_key_exists($call, self::$module_index))
        {
            $info = self::$module_index[$call]; $incl = array();
            if(count($info['xml_module_functions']) >= 1)
            {
                foreach($info['xml_module_functions'] as $file)
                { $incl[] = $info['xml_module_path'].'functions/'.$file; }
                unset($file);
            }

            if(count($info['xml_module_indexes'] )>= 1)
            {
                foreach($info['xml_module_indexes'] as $file)
                { $incl[] = $info['xml_module_path'].$file; }
                unset($file);
            }

            return $incl;
        }

        return false;
    }

    public static function module_call_function($call='')
    {
        if(array_key_exists($call, self::$module_index))
        {
            $info = self::$module_index[$call];
            return $info['xml_call_function'];
        }
    }
}

## Include Kernel Classes ##
if($files = kernel::list_background_files(ROOT_PATH . 'inc/class/',false,true,array("php")))
{ foreach($files as $func) { include_once(ROOT_PATH.'inc/class/'.$func); } } unset($files);