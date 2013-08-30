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

class DB_MySQLI extends DB_MGR
{
    protected static $config_array = array("multi_query" => array(), "runned_query" => array(), "query" => array());

    ## Config Schreiben ##
    public static function conf($conf="",$var="",$wire=false)
    { if($wire)self::$config_array[$conf] = $var; else return @self::$config_array[$conf]; }

    ## Connect ##
    ## Return: true/false ##
    private static function sql_connect($sqlrID=0)
    {
        if(self::conf('debug'))
        {
            printf("<p>Debug: Host: %s\n",self::conf('sqlhost'));
            printf("<p>Debug: Port: %s\n",self::conf('sqlport'));
            printf("<p>Debug: User: %s\n",self::conf('sqluser'));
            printf("<p>Debug: Passwort hat %s\n Zeichen",strlen(self::conf('sqlpass')));
            printf("<p>Debug: DB: %s\n",self::conf('sqldb') );
        }

        self::$config_array['con'][$sqlrID] = mysqli_connect(self::conf('sqlhost'), self::conf('sqluser'), self::conf('sqlpass'), self::conf('sqldb'), self::conf('sqlport'));

        if(self::conf('debug'))
        {
            printf("<p><p>Debug: Server Object:");
            echo "<pre>";
            print_r(self::$config_array['con'][$sqlrID]);
            echo "</pre>";
        }

        if(self::$config_array['con'][$sqlrID]->connect_errno)
        {
            if(self::conf('debug'))
                printf("<p>Debug: Server connect failed! %s\n", self::$config_array['con'][$sqlrID]->connect_error);

            return false;
        }

        return true;
    }

    ## Run SQL Query ##
    ## Return: query/false
    private static function run_query($sqlrID=0)
    {
        if(@mysqli_ping(self::$config_array['con'][$sqlrID]))
        {
            if(!isset(self::$config_array['query'][$sqlrID]))
                return false;

            if(empty(self::$config_array['query'][$sqlrID]))
                return false;

            if(self::conf('debug'))
            {
                if(self::$config_array['multi_query'][$sqlrID])
                {
                    printf("<p><p>Debug: Run Querys:");
                    echo "<pre>";
                    print_r(self::$config_array['multi_query'][$sqlrID]);
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
                { self::$config_array['runned_query'][$sqlrID][$i] = mysqli_query(self::$config_array['con'][$sqlrID],$array_query); $i++; }
            }
            else ## Singe Query ##
                self::$config_array['runned_query'][$sqlrID] = mysqli_query(self::$config_array['con'][$sqlrID],self::$config_array['query'][$sqlrID]);

            if(self::conf('debug'))
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
        if(self::conf('debug'))
            printf("<p>Debug: MySQLI Query<p>");

        if (!function_exists('mysqli_query') or !function_exists('mysqli_connect'))
        {
            printf("<p>Debug: MySQLI Extension not loaded!<p>");
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
            if(self::conf('debug'))
                printf("<p>Debug: Input MultiQuery<p>");

            $i=0; $array = array();
            foreach(self::$config_array['runned_query'][$sqlrID] as $runned_array_query)
            {
                switch ($type)
                {
                    case "assoc": $array[$i] = mysqli_fetch_assoc($runned_array_query); break;
                    case "array": $array[$i] = mysqli_fetch_array($runned_array_query,MYSQLI_NUM); break;
                    case "rows": $array[$i] = $runned_array_query->num_rows; break;
                    case "lastid": $array[$i] = mysqli_insert_id(self::$config_array['con'][$sqlrID]); break;
                    default: $array[$i] = $runned_array_query; break;
                }

                $i++; $runned_array_query->free();
            }

            self::$config_array['con'][$sqlrID]->close();
            return $array;
        }
        else
        {
            if(self::conf('debug'))
                printf("<p>Debug: Input SingeQuery<p>");

            switch ($type)
            {
                case "assoc": $data = mysqli_fetch_assoc(self::$config_array['runned_query'][$sqlrID]); break;
                case "array": $data = mysqli_fetch_array(self::$config_array['runned_query'][$sqlrID],MYSQLI_NUM); break;
                case "rows": $data = self::$config_array['runned_query'][$sqlrID]->num_rows; break;
                case "lastid": $data = mysqli_insert_id(self::$config_array['con'][$sqlrID]); break;
                default: $data = self::$config_array['runned_query'][$sqlrID]; break;
            }

            self::$config_array['con'][$sqlrID]->close();
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

        if(self::conf('debug'))
        {
            if(self::$config_array['multi_query'][$sqlrID])
            {
                printf("<p>Debug: Querys:");
                echo "<pre>";
                print_r(self::$config_array['multi_query'][$sqlrID]);
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