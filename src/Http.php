<?php
/**
 *
 * @author  qiangjian@staff.sina.com.cn sunsky303@gmail.com
 * Date: 2017/6/16
 * Time: 10:20
 * @version $Id: $
 * @since 1.0
 * @copyright Sina Corp.
 */

namespace sinacms\MultiHttp;



abstract class Http
{
    const HEAD      = 'HEAD';
    const GET       = 'GET';
    const POST      = 'POST';
    const PUT       = 'PUT';
    const DELETE    = 'DELETE';
    const PATCH     = 'PATCH';
    const OPTIONS   = 'OPTIONS';
    const TRACE     = 'TRACE';

    abstract function head($uri, array $payload = array(), array $options = array());
    abstract function get($uri, array $payload = array(), array $options = array());
    abstract function post($uri, array $payload = array(), array $options = array());
    abstract function put($uri, array $payload = array(), array $options = array());
    abstract function DELETE($uri, array $payload = array(), array $options = array());
    abstract function PATCH($uri, array $payload = array(), array $options = array());
    abstract function OPTIONS($uri, array $payload = array(), array $options = array());
    abstract function TRACE($uri, array $payload = array(), array $options = array());

    public static function isHaveBody()
    {
        return array(self::POST, self::PUT, self::PATCH, self::OPTIONS);
    }
    public static function safeMethods()
    {
        return array(self::HEAD, self::GET, self::OPTIONS, self::TRACE);
    }
}