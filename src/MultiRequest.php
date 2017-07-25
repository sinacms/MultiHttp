<?php

namespace MultiHttp;

use MultiHttp\Exception\InvalidArgumentException;

/**
 *
 * @author  qiangjian@staff.sina.com.cn sunsky303@gmail.com
 * Date: 2017/6/9
 * Time: 15:09
 * @version $Id: $
 * @since 1.0
 * @copyright Sina Corp.
 */
class MultiRequest
{
    /**
     * @var [Response]
     */
    protected static $requestPool;
    protected static $multiHandler;
    protected $defaultOptions = array();
    private static $instance;
    protected static $loggerHandler;
    /**
     * MultiRequest constructor.
     */
    protected function __construct()
    {
    }

    /**
     * @return MultiRequest
     */
    public static function create()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self;
        }
        self::prepare();
        return self::$instance;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setDefaults(array $options = array()){
        $this->defaultOptions = $options;
        return $this;
    }
    protected static function prepare()
    {
        self::$multiHandler = curl_multi_init();
    }

    /**
     * @param $method
     * @param $uri
     * @param $payload
     * @param array $options
     * @return $this
     */
    public function add($method, $uri, $payload, array $options = array())
    {
        $options = array(
                'method' => $method,
                'url' => $uri,
                'data' => $payload,
            ) + $options;
        $this->addOptions(array($options));
        return $this;
    }

    /**
     * @param array $URLOptions
     * example: array(array('url'=>'http://localhost:9999/','timeout'=>1, 'method'=>'POST', 'data'=>'aa=bb&c=d'))
     * @return $this
     */
    public function addOptions(array $URLOptions)
    {
        foreach ($URLOptions as $options) {
            $options = $options + $this->defaultOptions;
            $request = Request::create()->addOptions($options)->applyOptions();
            if (isset($options['callback'])) {
                $request->onEnd($options['callback']);
            }
            $this->import($request);
        }
        return $this;
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function import(Request $request)
    {
        if (!is_resource($request->curlHandle)) {
            throw new InvalidArgumentException('Request curl handle is not initialized');
        }
        curl_multi_add_handle(self::$multiHandler, $request->curlHandle);
        self::$requestPool[] = $request;
        return $this;
    }

    /**
     * @return array(Response)
     */
    public function execute()
    {
        $sleepTime = 1000;//microsecond, prevent  CPU 100%
        do {
            curl_multi_exec(self::$multiHandler, $active);
            // bug in PHP 5.3.18+ where curl_multi_select can return -1
            // https://bugs.php.net/bug.php?id=63411
            if (curl_multi_select(self::$multiHandler) == -1) {
                usleep($sleepTime);
            }
            usleep($sleepTime);
        } while ($active);
        $return = array();
        foreach (self::$requestPool as $request) {
            $return[] = $request->send(true);
            curl_multi_remove_handle(self::$multiHandler, $request->curlHandle);
            curl_close($request->curlHandle);
        }
        curl_multi_close(self::$multiHandler);
        self::$requestPool = null;
        return $return;
    }

}
