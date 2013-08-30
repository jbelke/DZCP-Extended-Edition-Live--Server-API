<?php
/**
 * <DZCP-Extended Edition - Live! Server>
 * @package: DZCP-Extended Edition
 * @author: Hammermaps.de Developer Team
 * @link: http://www.hammermaps.de
 */

## Check IN_SYS ##
if (!defined('IN_SYS')) exit();
define('runtime_buffer', true);

class kernel
{
    private static $passwordComponents = array("ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz","0123456789","#$@!"); //generatePW() Components
    private static $xmlobj = array(array()); //XML

    /**
    * Eine Liste der Dateien oder Verzeichnisse zusammenstellen, die sich im angegebenen Ordner befinden.
    *
    * @return array
    */
    public static function list_background_files($dir=null,$only_dir=false,$only_files=false,$file_ext=array(),$preg_match=false,$blacklist=array())
    {
        $files = array();
        if(!file_exists($dir) && !is_dir($dir)) return $files;
        $hash = md5($dir.$only_dir.$only_files.count($file_ext).$preg_match.count($blacklist));

        if(!RTBuffer::check($hash))
            return RTBuffer::get($hash);

        if($handle = @opendir($dir))
        {
            if($only_dir) ## Ordner ##
            {
                while(false !== ($file = readdir($handle)))
                {
                    if($file != '.' && $file != '..' && !is_file($dir.'/'.$file))
                    {
                        if(!count($blacklist) && ($preg_match ? preg_match($preg_match,$file) : true))
                            $files[] = $file;
                        else
                        {
                            if(!in_array($file, $blacklist) && ($preg_match ? preg_match($preg_match,$file) : true))
                                $files[] = $file;
                        }
                    }
                } //while end
            }
            else if($only_files) ## Dateien ##
            {
                while(false !== ($file = readdir($handle)))
                {
                    if($file != '.' && $file != '..' && is_file($dir.'/'.$file))
                    {
                        if(!in_array($file, $blacklist) && !count($file_ext) && ($preg_match ? preg_match($preg_match,$file) : true))
                            $files[] = $file;
                        else
                        {
                            ## Extension Filter ##
                            $exp_string = array_reverse(explode(".", $file));
                            if(!in_array($file, $blacklist) && in_array(strtolower($exp_string[0]), $file_ext) && ($preg_match ? preg_match($preg_match,$file) : true))
                                $files[] = $file;
                        }
                    }
                } //while end
            }
            else ## Ordner & Dateien ##
            {
                while(false !== ($file = readdir($handle)))
                {
                    if($file != '.' && $file != '..' && is_file($dir.'/'.$file))
                    {
                        if(!in_array($file, $blacklist) && !count($file_ext) && ($preg_match ? preg_match($preg_match,$file) : true))
                            $files[] = $file;
                        else
                        {
                            ## Extension Filter ##
                            $exp_string = array_reverse(explode(".", $file));
                            if(!in_array($file, $blacklist) && in_array(strtolower($exp_string[0]), $file_ext) && ($preg_match ? preg_match($preg_match,$file) : true))
                                $files[] = $file;
                        }
                    }
                    else
                    {
                        if(!in_array($file, $blacklist) && $file != '.' && $file != '..' && ($preg_match ? preg_match($preg_match,$file) : true))
                            $files[] = $file;
                    }
                } //while end
            }

            if(is_resource($handle))
                closedir($handle);

            if(!count($files))
                return false;

            RTBuffer::set($hash,$files);
            return $files;
        }
        else
            return false;
    }

    /**
     * Wandelt einen Json-String in ein Array um.
     *
     * @return array
     */
    public static function string_to_array($str='')
    { return json_decode($str, true); }

    /**
     * Wandelt einen Array in einen Json-String um.
     *
     * @return String
     */
    public static function array_to_string($arr=array())
    { return json_encode($arr,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); }

    /**
     * Funktion um eine Variable prüfung in einem Array durchzuführen
     *
     * @return boolean
     */
    function array_var_exists($var,$search)
    { foreach($search as $key => $var_) { if($var_==$var) return true; } return false; }

    /**
     * Generiert Passwörter
     *
     * @return String
     */
    public static function generatePW($passwordLength=8)
    {
        $password = "";
        shuffle(self::$passwordComponents);
        $componentsCount = count(self::$passwordComponents);

        for ($pos = 0; $pos < $passwordLength; $pos++)
        {
            $componentIndex = ($pos % $componentsCount);
            $componentLength = strlen(self::$passwordComponents[$componentIndex]);
            $random = rand(0, $componentLength-1);
            $password .= self::$passwordComponents[$componentIndex]{ $random };
         }

         return $password;
    }

    /**
     * Gibt die IP des Besuchers / Users zurück
     *
     * @return String
     */
    public static function visitorIp()
    {
        $TheIp=$_SERVER['REMOTE_ADDR'];
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            $TheIp = $_SERVER['HTTP_X_FORWARDED_FOR'];

        if(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']))
            $TheIp = $_SERVER['HTTP_CLIENT_IP'];

        if(isset($_SERVER['HTTP_FROM']) && !empty($_SERVER['HTTP_FROM']))
            $TheIp = $_SERVER['HTTP_FROM'];

        $TheIp_X = explode('.',$TheIp);
        if(count($TheIp_X) == 4 && $TheIp_X[0]<=255 && $TheIp_X[1]<=255 && $TheIp_X[2]<=255 && $TheIp_X[3]<=255 && preg_match("!^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$!",$TheIp))
            return trim($TheIp);

        return '0.0.0.0';
    }

    /**
     * Wandelt eine DNS Adresse in eine IPv4 um
     *
     * @return String / IPv4
     */
    public static function DNSToIp($address='')
    {
        if(!preg_match('#^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$#', $address))
        {
            $result = gethostbyname($address);
            if ($result === $address)
                $result = false;
        }
        else
            $result = $address;

        return $result;
    }

    /**
     * Funktion um notige Erweiterungen zu prufen
     *
     * @return boolean
     **/
    public static function fsockopen_support()
    {
        if(!function_exists('fsockopen'))
            return false;

        if(!function_exists("fopen"))
            return false;

        if(ini_get('allow_url_fopen') != 1)
            return false;

        if(strpos(ini_get('disable_functions'),'fsockopen') || strpos(ini_get('disable_functions'),'file_get_contents') || strpos(ini_get('disable_functions'),'fopen'))
            return false;

        return true;
    }

    /**
     * Pingt einen Server Port
     *
     * @return boolean
     **/
    public static function ping_port($address='',$port=0000,$timeout=2,$udp=false)
    {
        if(!self::fsockopen_support())
            return false;

        $errstr = NULL; $errno = NULL;
        if($fp = @fsockopen(($udp ? "udp://".DNSToIp($address) : DNSToIp($address)), $port, $errno, $errstr, $timeout))
        {
            unset($ip,$port,$errno,$errstr,$timeout);
            @fclose($fp);
            return true;
        }

        return false;
    }

    /**
     * Wird verwendet um die Ladezeit der Seite zu errechnen.
     *
     * @return float
     */
    public static function generatetime()
    {
        list($usec, $sec) = explode(" ",microtime());
        return ((float)$usec + (float)$sec);
    }
}

#############################################
#################### XML ####################
#############################################
class xml
{
    private static $xmlobj = array(array()); //XML

    /**
     * XML Datei Laden
    */
    public static function openXMLfile($XMLTag,$XMLFile,$oneModule=false)
    {
        if(empty($XMLTag) || empty($XMLFile)) return false;
        if(file_exists(ROOT_PATH . $XMLFile) || !$oneModule)
        {
            if(!array_key_exists($XMLTag,self::$xmlobj))
            {
                self::$xmlobj[$XMLTag]['xmlFile'] = $XMLFile;

                if(!$oneModule)
                {
                    if(!file_exists(ROOT_PATH . $XMLFile))
                        file_put_contents(ROOT_PATH . $XMLFile, '<?xml version="1.0"?><'.$XMLTag.'></'.$XMLTag.'>');
                }

                self::$xmlobj[$XMLTag]['objekt'] = simplexml_load_file(ROOT_PATH . $XMLFile);

                if(self::$xmlobj[$XMLTag]['objekt'] != false)
                    return true;
                else
                    return false;
            }
            else
                return true;
        }

        return false;
    }

    /**
     * XML Stream Laden
     */
    public static function openXMLStream($XMLTag,$XMLStream)
    {
        if(empty($XMLTag) || empty($XMLStream)) return false;
        if(!array_key_exists($XMLTag,self::$xmlobj))
        {
            self::$xmlobj[$XMLTag]['xmlFile'] = $XMLStream;
            self::$xmlobj[$XMLTag]['objekt'] = simplexml_load_string($XMLStream);

            if(self::$xmlobj[$XMLTag]['objekt'] != false)
                return true;
            else
                return false;
        }
        else
            return true;
    }

    /**
     * XML Wert auslesen
     *
     * @return XMLObj / boolean
     */
    public static function getXMLvalue($XMLTag, $xmlpath)
    {
        if(empty($XMLTag) || empty($xmlpath)) return false;
        if(array_key_exists($XMLTag,self::$xmlobj))
        {
            $xmlobj = self::$xmlobj[$XMLTag]['objekt']->xpath($xmlpath);
            return ($xmlobj) ? $xmlobj[0] : false;
        }
        else
            return false;
    }

    /**
     * XML Werte �ndern
     *
     * @return boolean
     */
    public static function changeXMLvalue($XMLTag, $xmlpath, $xmlnode, $xmlvalue='')
    {
        if(empty($XMLTag) || empty($xmlpath) || empty($xmlnode)) return false;
        if(array_key_exists($XMLTag,self::$xmlobj))
        {
            $xmlobj = self::$xmlobj[$XMLTag]['objekt']->xpath($xmlpath);
            $xmlobj[0]->{$xmlnode} = htmlspecialchars($xmlvalue);
            return true;
        }
        else
            return false;
    }

    /**
     * Einen neuen XML Knoten hinzuf�gen
     *
     * @return boolean
     */
    public static function createXMLnode($XMLTag, $xmlpath, $xmlnode, $attributes=array(), $text='')
    {
        if(empty($XMLTag) || empty($xmlpath) || empty($xmlnode)) return false;
        if(array_key_exists($XMLTag,self::$xmlobj))
        {
            $xmlobj = self::$xmlobj[$XMLTag]['objekt']->xpath($xmlpath);
            $xmlobj2 = $xmlobj[0]->addChild($xmlnode, htmlspecialchars($text));
            foreach($attributes as $attr => $value)
                $xmlobj2->addAttribute($attr, htmlspecialchars($value));
            return true;
        }
        else
            return false;
    }

    /**
     *  XML-Datei speichern
     *
     * @return boolean
     */
    public static function saveXMLfile($XMLTag)
    {
        if(empty($XMLTag)) return false;
        if(!array_key_exists($XMLTag,self::$xmlobj))
            return false;

        $xmlFileValue = self::$xmlobj[$XMLTag]['objekt']->asXML();
        file_put_contents(ROOT_PATH . self::$xmlobj[$XMLTag]['xmlFile'], $xmlFileValue);
        return true;
    }

    /**
     * Einen XML Knoten l�schen
     *
     * @return boolean
     */
    public static function deleteXMLnode($XMLTag, $xmlpath, $xmlnode)
    {
        if(empty($XMLTag) || empty($xmlpath) || empty($xmlnode)) return false;
        if(array_key_exists($XMLTag,self::$xmlobj))
        {
            $parent = self::getXMLvalue($XMLTag, $xmlpath);
            unset($parent->$xmlnode);
            return true;
        }
        else
            return false;
    }

    /**
     * Einen XML Knoten Attribut l�schen
     *
     * @return boolean
     */
    public static function deleteXMLattribut($XMLTag, $xmlpath, $key, $value )
    {
        if(empty($XMLTag) || empty($xmlpath) || empty($key) || empty($value)) return false;
        if(array_key_exists($XMLTag,self::$xmlobj))
        {
            $nodes = self::getXMLvalue($XMLTag, $xmlpath);
            foreach($nodes as $node)
            {
                if((string)$node->attributes()->$key==$value)
                {
                    unset($node[0]);
                    break;
                }
            }
            return true;
        }
        else
            return false;
    }

    /**
     * Einen XML Boolean umwandeln
     *
     * @return boolean
     */
    public static function bool($value)
    { return ($value == 'true' ? true : false); }
}

class convert
{
    public static final function ToString($input)
    { return (string)$input; }

    public static final function BoolToInt($input)
    { return ($input == true ? 1 : 0); }

    public static final function IntToBool($input)
    { return ($input == 0 ? false : true); }

    public static final function ToInt($input)
    { return (int)$input; }

    public static final function UTF8($input)
    { return self::ToString(utf8_encode($input)); }

    public static final function UTF8_Reverse($input)
    { return utf8_decode($input); }
}

class string
{
    /**
     * Funktion um Sonderzeichen zu konvertieren
     *
     * @return string
     */
    private static function spChars($txt,$reverse=false)
    {
        $var0 = array("€", "'", "\"");
        $var1 = array("&euro;","&apostroph;","&quot;");
        return self::spChars_uml($reverse ? str_replace($var1, $var0, $txt) : str_replace($var0, $var1, $txt),$reverse);
    }

    /**
     * Funktion um Umlaute in html Code umzuwandeln
     *
     * @return string
     */
    private static function spChars_uml($txt,$reverse=false)
    {
        $var0 = array("Ä", "Ö", "Ü", "ä", "ö", "ü", "ß");
        $var1 = array("&Auml;", "&Ouml;", "&Uuml;", "&auml;", "&ouml;", "&uuml;", "&szlig;");
        return $reverse ? str_replace($var1, $var0, $txt) : str_replace($var0, $var1, $txt);
    }

    /**
     * Codiert Text in das UTF8 Charset.
     *
     * @param string $txt
     */
    public static function encode($txt='')
    {
        return stripcslashes(self::spChars(convert::ToHTML($txt)));
    }

    /**
     * Decodiert UTF8 Text in das aktuelle Charset der Seite.
     *
     * @param utf8 string $txt
     */
    public static function decode($txt='')
    {
        return trim(stripslashes(self::spChars(html_entity_decode($txt, ENT_COMPAT, 'iso-8859-1'),true)));
    }
}

/**
 * Runtime Buffer
 * Funktion um Werte kurzzeitig zu speichern.
 */
final class RTBuffer
{
    protected static $buffer = array();
    public static final function set($tag='',$data='',$time=1)
    { self::$buffer[$tag]['ttl'] = (time()+$time); self::$buffer[$tag]['data'] = json_encode($data,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); }

    public static final function get($tag)
    { return (array_key_exists($tag, self::$buffer) ? json_decode(self::$buffer[$tag]['data']):false); }

    public static final function check($tag)
    { if(!runtime_buffer) return true; if(!array_key_exists($tag, self::$buffer)) return true; else if(self::$buffer[$tag]['ttl'] < time())
    { unset(self::$buffer[$tag]['data']); unset(self::$buffer[$tag]['ttl']); return true; } else return false; }
}