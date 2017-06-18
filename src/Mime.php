<?php
/**
 *
 * @author  qiangjian@staff.sina.com.cn sunsky303@gmail.com
 * Date: 2017/6/16
 * Time: 11:40
 * @version $Id: $
 * @since 1.0
 * @copyright Sina Corp.
 */

namespace sinacms\MultiHttp;



class Mime
{
    const JSON    = 'application/json';
    const XML     = 'application/xml';
    const XHTML   = 'application/html+xml';
    const FORM    = 'application/x-www-form-urlencoded';
    const UPLOAD  = 'multipart/form-data';
    const PLAIN   = 'text/plain';
    const JS      = 'text/javascript';
    const HTML    = 'text/html';
    const YAML    = 'application/x-yaml';
    const CSV     = 'text/csv';

    public static $mimes = array(
        'json'      => self::JSON,
        'xml'       => self::XML,
        'form'      => self::FORM,
        'plain'     => self::PLAIN,
        'text'      => self::PLAIN,
        'upload'      => self::UPLOAD,
        'html'      => self::HTML,
        'xhtml'     => self::XHTML,
        'js'        => self::JS,
        'javascript'=> self::JS,
        'yaml'      => self::YAML,
        'csv'       => self::CSV,
    );
    public static function fullName($short_name)
    {
        return isset(self::$mimes[$short_name]) ? self::$mimes[$short_name] : $short_name;
    }

    public static function isSupport($short_name)
    {
        return isset(self::$mimes[$short_name]);
    }
}