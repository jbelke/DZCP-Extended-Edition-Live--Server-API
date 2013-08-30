<?php
/**
 * <DZCP-Extended Edition - Live! Server>
 * @package: DZCP-Extended Edition
 * @author: Hammermaps.de Developer Team
 * @link: http://www.hammermaps.de

/*
### Encode ###
public function server_encode($input_array); # Return: false or hexcode #
public function server_encode_cryptkey($key); # Return: false or true #
public function set_options($key,$var) # Return: false or true #
*/

####################################################
################## Daten codieren ##################
####################################################
final class server_api_encode
{
    protected static $hex_stream_out = null;
    protected static $bz_stream_out = null;
    protected static $json_stream = null;
    protected static $input_array_in = null;
    protected static $mcrypt_string = null;
    protected static $crypt_key = null;
    private static $options = array();

    public static function init()
    {
        if (!extension_loaded('json')) die("Die JSON Erweiterung ist nicht geladen!");
        if (!extension_loaded('mcrypt')) die("Die Mcrypt Erweiterung ist nicht geladen!");

        self::$options['encode_hex'] = true;
        self::$options['encode_gzip'] = true;
        self::$options['encode_crypt'] = true;
        self::$options['encode_base'] = true;

        return true;
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

    public static function server_encode_cryptkey($key="")
    {
        if(empty($key)) return false;
        self::$crypt_key = md5($key);
        return (empty(self::$crypt_key) ? false : true);
    }

    public static function server_encode($input_array=array())
    {
        if(empty($input_array)) return false;
        self::$input_array_in = $input_array;
        if(!self::encode_base()) return false;
        if(!self::encode_crypt()) return false;
        if(!self::encode_gzip()) return false;
        if(!self::encode_hex()) return false;
        return self::$hex_stream_out;
    }

    private static function encode_base()
    {
        self::$json_stream = self::$options['encode_base'] ? json_encode(self::$input_array_in,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) : self::$input_array_in;
        return (empty(self::$json_stream) || !self::$json_stream ? false : true);
    }

    private static function encode_crypt()
    {
        if(empty(self::$json_stream)) return false;
        self::$options['encode_crypt'] ? self::encryptData() : (self::$mcrypt_string = self::$json_stream);
        return (empty(self::$mcrypt_string) || !self::$mcrypt_string ? false : true);
    }

    private static function encode_gzip()
    {
        if(empty(self::$mcrypt_string)) return false;
        self::$bz_stream_out = self::$options['encode_gzip'] ? gzcompress(self::$mcrypt_string) : self::$mcrypt_string;
        return (empty(self::$bz_stream_out) || !self::$bz_stream_out ? false : true);
    }

    private static function encode_hex()
    {
        if(empty(self::$bz_stream_out)) return false;
        self::$hex_stream_out = self::$options['encode_hex'] ? bin2hex(self::$bz_stream_out) : self::$bz_stream_out;
        return (empty(self::$hex_stream_out) || !self::$hex_stream_out ? false : true);
    }

    private static function encryptData()
    {
        if(empty(self::$json_stream)) return false;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        self::$mcrypt_string = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, self::$crypt_key, self::$json_stream, MCRYPT_MODE_ECB, $iv);
        if(!empty(self::$mcrypt_string) && self::$mcrypt_string != false) return true;
        return false;
    }
}