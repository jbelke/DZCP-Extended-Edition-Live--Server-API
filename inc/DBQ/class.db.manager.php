<?php
/**
 * <DZCP-Extended Edition - Live! Server>
 * @package: DZCP-Extended Edition
 * @author: Hammermaps.de Developer Team
 * @link: http://www.hammermaps.de
 */

## Check IN_SYS ##
if (!defined('IN_SYS')) exit();

## Include SQL Classes ##
require_once(ROOT_PATH . 'inc/DBQ/class.db.mssql.php');
require_once(ROOT_PATH . 'inc/DBQ/class.db.mysql.php');
require_once(ROOT_PATH . 'inc/DBQ/class.db.mysqli.php');
require_once(ROOT_PATH . 'inc/DBQ/class.db.sqlite.php');

## Run SQL Querys ##
function db($query="",$rows=false,$fetch=false,$remote_db_name=false)
{
    global $sql_config;
    $array = ($rows && $fetch ? true : false);
    if($remote_db_name != false) ## Remote DB Connect ##
    {
        $caseID = mt_rand(0, 1000);
        DB_MGR::set_sql_type($sql_config[$remote_db_name],$caseID);
        DB_MGR::mgr_sql_set_query($query,$sql_config[$remote_db_name],$caseID);

        if(!$rows && !$fetch && !$array)
            return DB_MGR::mgr_sql_get_query($sql_config[$remote_db_name],$caseID);
        else if(!$rows && $fetch && !$array)
            return DB_MGR::mgr_sql_fetch_assoc($sql_config[$remote_db_name],$caseID);
        else if(!$rows && !$fetch && $array)
            return DB_MGR::mgr_sql_fetch_array($sql_config[$remote_db_name],$caseID);
        else if($rows && !$fetch && !$array)
            return DB_MGR::mgr_sql_get_rows($sql_config[$remote_db_name],$caseID);
        else
            return DB_MGR::mgr_sql_get_query($sql_config[$remote_db_name],$caseID);
    }
    else
    {
        $caseID = mt_rand(0, 1000);
        DB_MGR::set_sql_type($sql_config['system'],$caseID);
        DB_MGR::mgr_sql_set_query($query,$sql_config['system'],$caseID);

        if(!$rows && !$fetch && !$array)
            return DB_MGR::mgr_sql_get_query($sql_config['system'],$caseID);
        else if(!$rows && $fetch && !$array)
            return DB_MGR::mgr_sql_fetch_assoc($sql_config['system'],$caseID);
        else if(!$rows && !$fetch && $array)
            return DB_MGR::mgr_sql_fetch_array($sql_config['system'],$caseID);
        else if($rows && !$fetch && !$array)
            return DB_MGR::mgr_sql_get_rows($sql_config['system'],$caseID);
        else
            return DB_MGR::mgr_sql_get_query($sql_config['system'],$caseID);
    }
}

## Get Last Insert ID ##
function db_last_insert($remote_db_name=false)
{
    global $sql_config;
    $caseID = mt_rand(0, 1000);
    if($remote_db_name != false) ## Remote DB Connect ##
    {
        DB_MGR::set_sql_type($sql_config[$remote_db_name]);
        return DB_MGR::mgr_sql_get_LastInsertId($sql_config[$remote_db_name],$caseID);
    }
    else
    {
        DB_MGR::set_sql_type($sql_config['system']);
        return DB_MGR::mgr_sql_get_LastInsertId($sql_config['system'],$caseID);
    }
}

## Funktion um diverse Dinge aus Tabellen auszaehlen zu lassen ##
function sql_cnt($table="", $where = "", $what = "id")
{
    $cnt = db("SELECT COUNT(".$what.") AS count FROM ".$table." ".$where,false,true);
    return ((int)$cnt['num']);
}

## Funktion um diverse Dinge aus Tabellen zusammenzaehlen zu lassen ##
function sql_sum($table="", $where = "", $what="")
{
    $cnt = db("SELECT SUM(".$what.") AS summe FROM ".$table." ".$where,false,true);
    return ((int)$cnt['num']);
}

## Settings auslesen ##
function sql_settings($what=array())
{
    if(is_array($what))
    {
        $sql="";
        foreach($what as $qy)
        { $sql .= $qy.", "; }
        $sql = substr($sql, 0, -2);
        return db("SELECT ".$sql." FROM `fw_settings`",false,true);
    }
    else
    {
        $get = db("SELECT ".$what." FROM `fw_settings`",false,true);
        return $get[$what];
    }
}

## Schleifen ##
function sql_fetch($fetch,$remote_db_name=false)
{
    global $sql_config;
    if($remote_db_name != false) ## Remote DB Connect ##
    {
        switch ($sql_config[$remote_db_name]['sqltype'])
        {
            default:
            case "mysqli":
                return mysqli_fetch_assoc($fetch);
            break;
            case "mysql":
                return mysql_fetch_assoc($fetch);
            break;
            case "mssql":
                return mssql_fetch_assoc($fetch);
            break;
            case "sqlite":
                return sqlite_fetch_all($fetch,SQLITE_ASSOC);
            break;
        }
    }
    else
    {
        switch ($sql_config['system']['sqltype'])
        {
            default:
            case "mysqli":
                return mysqli_fetch_assoc($fetch);
            break;
            case "mysql":
                return mysql_fetch_assoc($fetch);
            break;
            case "mssql":
                return mssql_fetch_assoc($fetch);
            break;
            case "sqlite":
                return sqlite_fetch_all($fetch,SQLITE_ASSOC);
            break;
        }
    }
}

function sql_rows($rows,$remote_db_name=false)
{
    global $sql_config;
    if($remote_db_name != false) ## Remote DB Connect ##
    {
        switch ($sql_config[$remote_db_name]['sqltype'])
        {
            default:
            case "mysqli":
                return mysqli_num_rows($rows);
            break;
            case "mysql":
                return mysql_num_rows($rows);
            break;
            case "mssql":
                return mssql_num_rows($rows);
            break;
            case "sqlite":
                return sqlite_num_rows($rows);
            break;
        }
    }
    else
    {
        switch ($sql_config['system']['sqltype'])
        {
            default:
            case "mysqli":
                return mysqli_num_rows($rows);
            break;
            case "mysql":
                return mysql_num_rows($rows);
            break;
            case "mssql":
                return mssql_num_rows($rows);
            break;
            case "sqlite":
                return sqlite_num_rows($rows);
            break;
        }
    }
}

## Datenbank Optimierung ##
function sql_optimize_db($remote_db_name=false)
{
    set_time_limit(2*60*60); //Max 2H
    global $sql_config;
    if($remote_db_name != false) ## Remote DB Connect ##
    {
        switch ($sql_config[$remote_db_name]['sqltype'])
        {
            case "mysqli":
                $dbs = db("SHOW TABLES",false,false,$remote_db_name);
                while($db = mysqli_fetch_array($dbs,MYSQLI_NUM))
                { db("OPTIMIZE TABLE ".$db[0]); }
                return true;
            break;
            case "mysql":
                $dbs = db("SHOW TABLES",false,false,$remote_db_name);
                while($db = mysql_fetch_array($dbs))
                { db("OPTIMIZE TABLE ".$db[0]); }
                return true;
            break;
            default:
                return true;
            break;
        }
    }
    else
    {
        switch ($sql_config['system']['sqltype'])
        {
            case "mysqli":
                $dbs = db("SHOW TABLES");
                while($db = mysqli_fetch_array($dbs,MYSQLI_NUM))
                { db("OPTIMIZE TABLE ".$db[0]); }
                return true;
            break;
            case "mysql":
                $dbs = db("SHOW TABLES");
                while($db = mysql_fetch_array($dbs))
                { db("OPTIMIZE TABLE ".$db[0]); }
                return true;
            break;
            default:
                return true;
            break;
        }
    }
}

## Managment Class ##
class DB_MGR
{
    public static function set_sql_type($sqlconfig=array(),$caseID=0)
    {
        switch ($sqlconfig['sqltype'])
        {
            default:
            case "mysqli":
                DB_MySQLI::conf("sqlport",$sqlconfig['port'],true);
                DB_MySQLI::conf("sqlpass",$sqlconfig['pass'],true);
                DB_MySQLI::conf("sqluser",$sqlconfig['user'],true);
                DB_MySQLI::conf("sqlhost",$sqlconfig['host'],true);
                DB_MySQLI::conf("sqldb",$sqlconfig['db'],true);
                DB_MySQLI::conf("debug",$sqlconfig['debug'],true);
            break;
            case "mysql":
                DB_MySQL::conf("pem",$sqlconfig['persistent'],true,$caseID);
                DB_MySQL::conf("sqlport",$sqlconfig['port'],true,$caseID);
                DB_MySQL::conf("sqlpass",$sqlconfig['pass'],true,$caseID);
                DB_MySQL::conf("sqluser",$sqlconfig['user'],true,$caseID);
                DB_MySQL::conf("sqlhost",$sqlconfig['host'],true,$caseID);
                DB_MySQL::conf("sqldb",$sqlconfig['db'],true,$caseID);
                DB_MySQL::conf("debug",$sqlconfig['debug'],true,$caseID);
            break;
            case "mssql":
                DB_MSSQL::conf("pem",$sqlconfig['persistent'],true,$caseID);
                DB_MSSQL::conf("sqlport",$sqlconfig['port'],true,$caseID);
                DB_MSSQL::conf("sqlpass",$sqlconfig['pass'],true,$caseID);
                DB_MSSQL::conf("sqluser",$sqlconfig['user'],true,$caseID);
                DB_MSSQL::conf("sqlhost",$sqlconfig['host'],true,$caseID);
                DB_MSSQL::conf("sqldb",$sqlconfig['db'],true,$caseID);
                DB_MSSQL::conf("debug",$sqlconfig['debug'],true,$caseID);
            break;
            case "sqlite":
                DB_SQLite::conf("pem",$sqlconfig['persistent'],true,$caseID);
                DB_SQLite::conf("sqlfile",$sqlconfig['db'],true,$caseID);
                DB_SQLite::conf("debug",$sqlconfig['debug'],true,$caseID);
            break;
        }
    }

    ## Return: false/Querylink ##
    public static function mgr_sql_get_query($sqlconfig=array(),$sqlrID=0)
    {
        switch ($sqlconfig['sqltype'])
        {
            default:
            case "mysqli":
                return DB_MySQLI::sql_get_query($sqlrID);
            break;
            case "mysql":
                return DB_MySQL::sql_get_query($sqlrID);
            break;
            case "mssql":
                return DB_MSSQL::sql_get_query($sqlrID);
            break;
            case "sqlite":
                return DB_SQLite::sql_get_query($sqlrID);
            break;
        }
    }

    ## Return: false/true ##
    public static function mgr_sql_set_query($query="",$sqlconfig=array(),$sqlrID=0)
    {
        switch ($sqlconfig['sqltype'])
        {
            default:
            case "mysqli":
                return DB_MySQLI::sql_set_query($query,$sqlrID);
            break;
            case "mysql":
                return DB_MySQL::sql_set_query($query,$sqlrID);
            break;
            case "mssql":
                return DB_MSSQL::sql_set_query($query,$sqlrID);
            break;
            case "sqlite":
                return DB_SQLite::sql_set_query($query,$sqlrID);
            break;
        }
    }

    ## Return: false/string Array ##
    public static function mgr_sql_fetch_assoc($sqlconfig=array(),$sqlrID=0)
    {
        switch ($sqlconfig['sqltype'])
        {
            default:
            case "mysqli":
                return DB_MySQLI::sql_fetch_assoc($sqlrID);
            break;
            case "mysql":
                return DB_MySQL::sql_fetch_assoc($sqlrID);
            break;
            case "mssql":
                return DB_MSSQL::sql_fetch_assoc($sqlrID);
            break;
            case "sqlite":
                return DB_SQLite::sql_fetch_assoc($sqlrID);
            break;
        }
    }

    ## Return: false/numberic Array ##
    public static function mgr_sql_fetch_array($sqlconfig=array(),$sqlrID=0)
    {
        switch ($sqlconfig['sqltype'])
        {
            default:
            case "mysqli":
                return DB_MySQLI::sql_fetch_array($sqlrID);
            break;
            case "mysql":
                return DB_MySQL::sql_fetch_array($sqlrID);
            break;
            case "mssql":
                return DB_MSSQL::sql_fetch_array($sqlrID);
            break;
            case "sqlite":
                return DB_SQLite::sql_fetch_array($sqlrID);
            break;
        }
    }

    ## Return: false/summ of rows ##
    public static function mgr_sql_get_rows($sqlconfig=array(),$sqlrID=0)
    {
        switch ($sqlconfig['sqltype'])
        {
            default:
            case "mysqli":
                return DB_MySQLI::sql_get_rows($sqlrID);
            break;
            case "mysql":
                return DB_MySQL::sql_get_rows($sqlrID);
            break;
            case "mssql":
                return DB_MSSQL::sql_get_rows($sqlrID);
            break;
            case "sqlite":
                return DB_SQLite::sql_get_rows($sqlrID);
            break;
        }
    }

    ## Return: false/last insert id ##
    public static function mgr_sql_get_LastInsertId($sqlconfig=array(),$sqlrID=0)
    {
        switch ($sqlconfig['sqltype'])
        {
            default:
            case "mysqli":
                return DB_MySQLI::sql_get_LastInsertId($sqlrID);
            break;
            case "mysql":
                return DB_MySQL::sql_get_LastInsertId($sqlrID);
            break;
            case "mssql":
                return DB_MSSQL::sql_get_LastInsertId($sqlrID);
            break;
            case "sqlite":
                return DB_SQLite::sql_get_LastInsertId($sqlrID);
            break;
        }
    }
}