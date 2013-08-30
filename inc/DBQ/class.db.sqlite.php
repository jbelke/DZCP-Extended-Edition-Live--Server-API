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

class DB_SQLite extends DB_MGR
{
    protected static $config_array = array("multi_query" => array(), "runned_query" => array(), "query" => array());

    ## Config Schreiben ##
    public static function conf($conf="",$var="",$wire=false,$sqlrID=0)
    { if($wire)self::$config_array[$sqlrID][$conf] = $var; else return self::$config_array[$sqlrID][$conf]; }

    ## Connect ##
    ## Return: true/false ##
    private static function sql_connect($sqlrID=0)
    {
        if(self::conf('debug',null,false,$sqlrID))
            printf("<p>Debug: File: %s\n",self::conf('sqlfile',null,false,$sqlrID));

        if(self::conf('pem',null,false,$sqlrID))
            self::$config_array['con'][$sqlrID] = sqlite_open(ROOT_PATH . self::conf('sqlfile'), 0666, self::$config_array['sqliteerror'][$sqlrID]);
        else
            self::$config_array['con'][$sqlrID] = sqlite_popen(ROOT_PATH . self::conf('sqlfile'), 0666, $sqliteerror);

        if(sqlite_last_error(self::$config_array['sqliteerror'][$sqlrID]))
        {
            if(self::conf('debug',null,false,$sqlrID))
                printf("<p>Debug: Server connect failed! %s\n", sqlite_error_string(sqlite_last_error(self::$config_array['sqliteerror'][$sqlrID])));

            return false;
        }

        return true;
    }

    ## Run SQL Query ##
    ## Return: query/false
    private static function run_query($sqlrID=0)
    {
        if(empty(self::$config_array['sqliteerror'][$sqlrID]))
        {
            if(!isset(self::$config_array['query'][$sqlrID]))
                return false;

            if(empty(self::$config_array['query'][$sqlrID]))
                return false;

            if(self::conf('debug',null,false,$sqlrID))
            {
                if(self::$config_array['multi_query'][$sqlrID])
                {
                    printf("<p><p>Debug: Run Querys:");
                    echo "<pre>";
                    print_r(self::$config_array['query'][$sqlrID]);
                    echo "</pre>";
                }
                else
                    printf("<p>Debug: Run Query:<p> %s\n", self::$config_array['query'][$sqlrID]);
            }

            if(self::$config_array['multi_query'][$sqlrID])
            {
                ## Multi-Query ##
                $i=0; self::$config_array['runned_query'][$sqlrID] = array();
                foreach(self::$config_array['query'][$sqlrID] as $array_query)
                { self::$config_array['runned_query'][$sqlrID][$i] = sqlite_query(self::$config_array['con'][$sqlrID],$array_query); $i++; }
            }
            else ## Singe Query ##
                self::$config_array['runned_query'][$sqlrID] = sqlite_query(self::$config_array['con'][$sqlrID],self::$config_array['query'][$sqlrID]);

            if(self::conf('debug',null,false,$sqlrID))
            {
                if(self::$config_array['runned_query'][$sqlrID])
                    echo "<p>Query Sended!";
                else
                    echo "<p>Error in Query Send!";
            }

            if(self::$config_array['runned_query'][$sqlrID])
                return true;
        }
        else
            return false;
    }

    ## Run assoc,array,rows and LastID ##
    private static function run_result_controler($type,$sqlrID=0)
    {
        if(self::conf('debug',null,false,$sqlrID))
            printf("<p>Debug: SQLite Query<p>");

        if (!function_exists('sqlite_query') or !function_exists('sqlite_open'))
        {
            printf("<p>Debug: SQLite Extension not loaded!<p>");
            return false;
        }

        if(!self::sql_connect($sqlrID))
            return false;

        if(!self::run_query($sqlrID))
            return false;

        if(!isset(self::$config_array['runned_query'][$sqlrID]) or empty(self::$config_array['runned_query'][$sqlrID]))
            return false;

        if(self::$config_array['multi_query'][$sqlrID])
        {
            if(self::conf('debug',null,false,$sqlrID))
                printf("<p>Debug: Input MultiQuery<p>");

            $i=0; $array = array();
            foreach(self::$config_array['runned_query'][$sqlrID] as $runned_array_query)
            {
                switch ($type)
                {
                    case "assoc": $array[$i] = sqlite_fetch_all($runned_array_query,SQLITE_ASSOC); break;
                    case "array": $array[$i] = sqlite_fetch_array($runned_array_query,SQLITE_NUM); break;
                    case "rows": $array[$i] = sqlite_num_rows($runned_array_query); break;
                    case "lastid": $array[$i] = sqlite_last_insert_rowid(self::$config_array['con'][$sqlrID]); break;
                    default: $array[$i] = $runned_array_query; break;
                }

                $i++;
            }

            sqlite_close(self::$config_array['con'][$sqlrID]);
            return $array;
        }
        else
        {
            if(self::conf('debug',null,false,$sqlrID))
                printf("<p>Debug: Input SingeQuery<p>");

            switch ($type)
            {
                case "assoc": $data = sqlite_fetch_all(self::$config_array['runned_query'][$sqlrID],SQLITE_ASSOC); break;
                case "array": $data = sqlite_fetch_array(self::$config_array['runned_query'][$sqlrID],SQLITE_NUM); break;
                case "rows": $data = sqlite_num_rows(self::$config_array['runned_query'][$sqlrID]); break;
                case "lastid": $data = sqlite_last_insert_rowid(self::$config_array['con'][$sqlrID]); break;
                default: $array[$i] = $runned_array_query; break;
            }

            sqlite_close(self::$config_array['con'][$sqlrID]);
            return $data;
        }
    }

    ## Public Functions ##
    ## Input: String OR Array ##
    ## Return: true/false
    public static function sql_set_query($query,$sqlrID=0)
    {
        if(!is_array($query) && !is_string($query))
            return false;
        else
            self::$config_array['query'][$sqlrID] = $query;

        if(is_array($query))
            self::$config_array['multi_query'][$sqlrID] = true;
        else
            self::$config_array['multi_query'][$sqlrID] = false;

        if(self::conf('debug',null,false,$sqlrID))
        {
            if(self::$config_array['multi_query'][$sqlrID])
            {
                printf("<p>Debug: Querys:");
                echo "<pre>";
                print_r(self::$config_array['query'][$sqlrID]);
                echo "</pre><p>";
            }
            else
                printf("<p>Debug: Query: %s\n", self::$config_array['query'][$sqlrID]);
        }

        return true;
    }

    ## Return: false/Querylink ##
    public static function sql_get_query($sqlrID=0)
    { return self::run_result_controler("query",$sqlrID); }

    ## Return: false/string Array ##
    public static function sql_fetch_assoc($sqlrID=0)
    { return self::run_result_controler("assoc",$sqlrID); }

    ## Return: false/numberic Array ##
    public static function sql_fetch_array($sqlrID=0)
    { return self::run_result_controler("array",$sqlrID); }

    ## Return: false/summ of rows ##
    public static function sql_get_rows($sqlrID=0)
    { return self::run_result_controler("rows",$sqlrID); }

    ## Return: false/last insert id ##
    public static function sql_get_LastInsertId($sqlrID=0)
    { return self::run_result_controler("lastid",$sqlrID); }
}
?>