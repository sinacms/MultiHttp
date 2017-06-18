<?php
/**
 *
 * @author  qiangjian@staff.sina.com.cn sunsky303@gmail.com
 * Date: 2017/6/18
 * Time: 15:14
 * @version $Id: $
 * @since 1.0
 * @copyright Sina Corp.
 */

namespace sinacms\MultiHttp;


class Request extends Http
{
    protected $curl;

    protected function __construct()
    {
        $this->curl = Curl::create();
    }

    public static function create()
    {
        return new self;
    }

    public function post($uri, array $payload = array(), array $options = array())
    {
        return $this->curl->ini(Http::POST, $uri, $payload, $options)->execute();
    }

    public function get($uri, array $payload = array(), array $options = array())
    {
        if (!empty($payload)) {
            $uri .= strpos($uri, '?') === FALSE ? '?' : '&';
            $uri .= http_build_query($payload);
        }
        return $this->ini(Http::GET, $uri, $payload, $options)->execute();
    }

    function head($uri, array $payload = array(), array $options = array())
    {
        return $this->curl->ini(Http::HEAD, $uri, $payload, $options)->execute();
    }

    function put($uri, array $payload = array(), array $options = array())
    {
        return $this->curl->ini(Http::PUT, $uri, $payload, $options)->execute();
    }

    function delete($uri, array $payload = array(), array $options = array())
    {
        return $this->curl->ini(Http::DELETE, $uri, $payload, $options)->execute();
    }

    function patch($uri, array $payload = array(), array $options = array())
    {
        return $this->curl->ini(Http::PATCH, $uri, $payload, $options)->execute();
    }

    function options($uri, array $payload = array(), array $options = array())
    {
        return $this->curl->ini(Http::OPTIONS, $uri, $payload, $options)->execute();
    }

    function trace($uri, array $payload = array(), array $options = array())
    {
        return $this->curl->ini(Http::TRACE, $uri, $payload, $options)->execute();
    }
}