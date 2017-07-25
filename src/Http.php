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

namespace MultiHttp;

/**
 * Class Http
 * @package MultiHttp
 */
abstract class Http
{
    const HEAD = 'HEAD';
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const PATCH = 'PATCH';
    const OPTIONS = 'OPTIONS';
    const TRACE = 'TRACE';
    public static $methods = array(
        'HEAD' => self::HEAD,
        'GET' => self::GET,
        'POST' => self::POST,
        'PUT' => self::PUT,
        'DELETE' => self::DELETE,
        'PATCH' => self::PATCH,
        'OPTIONS' => self::OPTIONS,
        'TRACE' => self::TRACE,
    );

    /**
     * @param $uri
     * @param null $payload
     * @param array $options
     * @return mixed
     */
    abstract function post($uri, $payload = null, array $options = array());

    /**
     * @param $uri
     * @param null $payload
     * @param array $options
     * @return mixed
     */
    abstract function patch($uri, $payload = null, array $options = array());

    /**
     * @param $uri
     * @param null $payload
     * @param array $options
     * @return mixed
     */
    abstract function put($uri, $payload = null, array $options = array());

    /**
     * @param $uri
     * @param array $options
     * @return mixed
     */
    abstract function get($uri, array $options = array());

    /**
     * @param $uri
     * @param array $options
     * @return mixed
     */
    abstract function head($uri, array $options = array());

    /**
     * @param $uri
     * @param array $options
     * @return mixed
     */
    abstract function delete($uri, array $options = array());

    /**
     * @param $uri
     * @param array $options
     * @return mixed
     */
    abstract function options($uri, array $options = array());

    /**
     * @param $uri
     * @param array $options
     * @return mixed
     */
    abstract function trace($uri, array $options = array());

}