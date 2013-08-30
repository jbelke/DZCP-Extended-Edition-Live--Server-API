<?php
/**
 * <DZCP-Extended Edition - Live! Server>
 * @package: DZCP-Extended Edition
 * @author: Hammermaps.de Developer Team
 * @link: http://www.hammermaps.de
 */

## Check IN_SYS ##
if (!defined('IN_SYS')) exit();

## Anti-Injection Protect ##
final class secure
{
    private static function callFilter($str)
    {
        if (is_array($str))
        {
            foreach($str AS $id => $value)
            {
                $str[$id] = self::secure($value);
            }
        }
        else
        {
            if(strpos(str_replace("''",""," $str"),"'")!=false)
                $str = str_replace("'", "''", $str);
        }

        return $str;
    }

    public static function run()
    {
        ## GET Filter ##
        if(isset($_GET))
        {
            $get_filter = array_keys($_GET);
            $i=0;

            while($i<count($get_filter))
            {
                $_GET[$get_filter[$i]]=self::callFilter($_GET[$get_filter[$i]]);
                $i++;
            }

            unset($get_filter); //Cleanup
        }

        ## REQUEST Filter ##
        if(isset($_REQUEST))
        {
            $req_filter = array_keys($_REQUEST);
            $i=0;

            while($i<count($req_filter))
            {
                $_REQUEST[$req_filter[$i]]=self::callFilter($_REQUEST[$req_filter[$i]]);
                $i++;
            }

            unset($req_filter); //Cleanup
        }

        ## POST Filter ##
        if(isset($_POST))
        {
            $post_filter = array_keys($_POST);
            $i=0;

            while($i<count($post_filter))
            {
                $_POST[$post_filter[$i]]=self::callFilter($_POST[$post_filter[$i]]);
                $i++;
            }

            unset($post_filter); //Cleanup
        }

        ## COOKIE Filter ##
        if(isset($_COOKIE))
        {
            $cookie_filter = array_keys($_COOKIE);
            $i=0;

            while($i<count($cookie_filter))
            {
                $_COOKIE[$cookie_filter[$i]]=self::callFilter($_COOKIE[$cookie_filter[$i]]);
                $i++;
            }

            unset($cookie_filter); //Cleanup
        }
    }
}

## CALL Secure ##
secure::run();