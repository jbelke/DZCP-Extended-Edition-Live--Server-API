<?php
/**
 * <DZCP-Extended Edition - Live! Server>
 * @package: DZCP-Extended Edition
 * @author: Hammermaps.de Developer Team
 * @link: http://www.hammermaps.de

/*
### Decode ###
public function server_decode($bzip_stream); # Return: false or array() #
public function server_decode_cryptkey($key); # Return: false or true #
public function set_options($key,$var) # Return: false or true #
*/

####################################################
################# Daten decodieren #################
####################################################
final class server_api_decode
{
    protected static $hex_stream_in = null;
    protected static $gz_stream = null;
    protected static $json_stream = null;
    protected static $mcrypt_string = null;
    protected static $crypt_key = null;
    protected static $output = null;
    private static $options = array();

    public static function init()
    {
        if (!extension_loaded('json'))
            die("Die JSON Erweiterung ist nicht geladen!");

        if (!extension_loaded('mcrypt'))
            die("Die Mcrypt Erweiterung ist nicht geladen!");

        self::$options['decode_hex'] = true;
        self::$options['decode_gzip'] = true;
        self::$options['decode_crypt'] = true;
        self::$options['decode_base'] = true;
    }

    public static function server_decode_cryptkey($key="")
    {
        if(empty($key)) return false;
        self::$crypt_key = md5($key);
        return (empty(self::$crypt_key) ? false : true);
    }

    public static function set_options($key="",$var='')
    {
        if(array_key_exists($key, self::$options))
        {
            self::$options[$key] = $var;
            return true;
        }

        return false;
    }

    public static function server_decode($hex_stream=null)
    {
        self::$hex_stream_in = $hex_stream;
        if(empty(self::$hex_stream_in)) return false;

        if(!self::decode_hex()) return false;
            self::$hex_stream_in = null;

        if(!self::decode_gzip()) return false;
            self::$gz_stream = null;

        if(!self::decode_crypt()) return false;
            self::$mcrypt_string = null;

        if(!self::decode_base()) return false;
            self::$json_stream = null;

        return self::$output;
    }

    private static function decode_base()
    {
        self::$output = self::$options['decode_base'] ? json_decode(self::$json_stream,true) : self::$json_stream;
        return (empty(self::$output) || !is_array(self::$output) || !self::$output ? false : true);
    }

    private static function decode_crypt()
    {
        if(empty(self::$mcrypt_string)) return false;
        self::$json_stream = self::$options['decode_crypt'] ? self::decryptData() : self::$mcrypt_string;
        return (empty(self::$json_stream) ? false : true);
    }

    private static function decode_gzip()
    {
        if(empty(self::$gz_stream)) return false;
        self::$mcrypt_string = self::$options['decode_gzip'] ? @gzuncompress(self::$gz_stream) : self::$gz_stream;
        return (empty(self::$mcrypt_string) || !self::$mcrypt_string ? false : true);
    }

    private static function decode_hex()
    {
        if(empty(self::$hex_stream_in)) return false;
        self::$gz_stream = self::$options['decode_hex'] ? hex2bin(self::$hex_stream_in) : self::$hex_stream_in;
        return (empty(self::$gz_stream) || !self::$gz_stream ? false : true);
    }

    private static function decryptData()
    {
        if(empty(self::$mcrypt_string)) return false;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, self::$crypt_key, self::$mcrypt_string, MCRYPT_MODE_ECB, $iv);
        self::$json_stream = trim($decrypttext);
        if(!empty(self::$json_stream) && self::$json_stream != false ) return true;
        return false;
    }
}