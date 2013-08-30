<?php
/**
 * <DZCP-Extended Edition - Live! Server>
 * @package: DZCP-Extended Edition
 * @author: Hammermaps.de Developer Team
 * @link: http://www.hammermaps.de
 */

class server_logic
{
    private static $stream = null; //Hex + Control
    private static $data_stream = null; //Hex etc.
    private static $data = array();
    private static $options = array();
    private static $cryptkey = '';
    private static $module_data = array();
    private static $ident = '';

    public static function caller()
    {
        self::$module_data = self::decode($_POST['input']);
        self::$module_data = module(self::$module_data);
        echo self::encode(self::$module_data);
    }

    public static function set_api_cryptkey($cryptkey='')
    { self::$cryptkey = $cryptkey; }

    public static function get_api_ident()
    { return self::$ident; }

    public static function get_api_options($key='decode_crypt')
    { return self::$options[$key]; }

    public static final function encode($data='')
    {
        self::$data = $data;
        if(!server_api_encode::init()) return false;

        if(!empty(self::$cryptkey))
            server_api_encode::server_encode_cryptkey(self::$cryptkey);
        else
            server_api_encode::set_options('encode_crypt',false);

        self::$options['encode_hex'] = self::$options['decode_hex'];
        self::$options['encode_gzip'] = self::$options['decode_gzip'];
        self::$options['encode_crypt'] = !empty(self::$cryptkey) ? true : false;
        self::$options['encode_base'] = is_array(self::$data) ? true : false;
        self::$options['file_stream'] = false;

        //Encode
        server_api_encode::set_options('encode_hex',self::$options['encode_hex']);
        server_api_encode::set_options('encode_gzip',self::$options['encode_gzip']);
        server_api_encode::set_options('encode_crypt',self::$options['encode_crypt']);
        server_api_encode::set_options('encode_base',self::$options['encode_base']);
        self::$data_stream = server_api_encode::server_encode(self::$data); self::$data = null;
        if(!empty(self::$data_stream) && self::$data_stream != false)
        {
            if(!self::wire_control()) return false;
            return (!empty(self::$stream) && self::$stream != false) ? self::$stream : false;
        }

        return false;
    }

    public static final function decode($stream)
    {
        self::$stream = $stream;
        if(!server_api_decode::init()) return false;
        if(!self::read_control()) return false;

        if(self::$options['decode_crypt'])
            server_api_decode::server_decode_cryptkey(self::$cryptkey);

        server_api_decode::set_options('decode_hex',self::$options['decode_hex']);
        server_api_decode::set_options('decode_gzip',self::$options['decode_gzip']);
        server_api_decode::set_options('decode_crypt',self::$options['decode_crypt']);
        server_api_decode::set_options('decode_base',self::$options['decode_base']);
        self::$data = server_api_decode::server_decode(self::$data_stream); self::$data_stream = null;
        if(!empty(self::$data) && self::$data != false) return self::$data;
        return false;
    }

    private static final function wire_control()
    {
        global $time_start;
        self::$stream =
        (self::$options['encode_hex'] ? '1' : '0').'|'. // Hex
        (self::$options['encode_gzip'] ? '1' : '0').'|'. // GZip
        (self::$options['encode_crypt'] ? '1' : '0').'|'. // Crypt
        (self::$options['encode_base'] ? '1' : '0').'|'. // JSON
        (self::$options['file_stream'] ? '1' : '0').'|'. // File Stream
        (getmicrotime() - $time_start).'|'. // Prozesstime
        self::$ident.'|'. // Return Ident
        self::$data_stream; // Data
        self::$data_stream = null;
        if(!empty(self::$stream) && self::$stream != false) return true;
        return false;
    }

    private static final function read_control()
    {
        $data = explode('|', self::$stream, 8);
        self::$options['decode_hex'] = convert::IntToBool($data[0]);
        self::$options['decode_gzip'] = convert::IntToBool($data[1]);
        self::$options['decode_crypt'] = convert::IntToBool($data[2]);
        self::$options['decode_base'] = convert::IntToBool($data[3]);
        self::$options['file_stream'] = convert::IntToBool($data[4]);
        self::$options['prozesstime'] = convert::ToString($data[5]);
        self::$ident = $data[6];
        self::$data_stream = $data[7]; unset($data); self::$stream = null;
        if(!empty(self::$data_stream) && self::$data_stream != false) return true;
        return false;
    }
}