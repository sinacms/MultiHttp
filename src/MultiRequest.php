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
    protected static $requestPool;
    protected static $multiHandler;
    protected $beginningCallback;

    protected function __construct()
    {
        self::$multiHandler = curl_multi_init();
    }

    private static $instance;

    public static function create()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * @param array $URLOptions
     * example: array(array('url'=>'http://localhost:9999/','timeout'=>1, 'method'=>'POST', 'data'=>'aa=bb&c=d'))
     * @return $this
     */
    public function addOptions(array $URLOptions)
    {
        foreach ($URLOptions as $options) {
            $request = Request::create()->addOptions($options)->applyOptions();
            if (isset($options['callback'])) {
                $request->onEnd($options['callback']);
            }
            $this->import($request);
        }
        return $this;
    }

    public function add($method, $uri, array $payload = array(), array $options = array())
    {
        $options = array(
                'method' => $method,
                'url' => $uri,
                'data' => $payload,
            ) + $options;
        $this->addOptions(array($options));
        return $this;
    }

    public function import(Request $request)
    {
        if (!is_resource($request->curlHandle)) throw new InvalidArgumentException('Request curl handle is not initialized');
        curl_multi_add_handle(self::$multiHandler, $request->curlHandle);
        self::$requestPool[] = $request;
        return $this;
    }


    public function onBeginning(callable $async)
    {
        $this->beginningCallback = $async;
        return $this;
    }



    /**
     * @return array(Response)
     */
    public function execute()
    {
        $running = null;
        $concurrency = false;
        $sleepTime = $this->beginningCallback ?  100 : 5000;//microsecond, prevent  CPU 100%
        do {
            curl_multi_exec(self::$multiHandler, $running);
            if (!$concurrency && $this->beginningCallback) {
                $func = $this->beginningCallback;
                $func();
            }
            $concurrency = true;
            // Wait for activity on any curl-connection
            if (curl_multi_select(self::$multiHandler) == -1) {
                usleep($sleepTime);
            }
            if (false !== ($info = curl_multi_info_read(self::$multiHandler))) {
                if (isset($info['handle']) && is_resource($info['handle'])) {
                    foreach (self::$requestPool as $request) {
                        if ($request->hasEndCallback() && $info['handle'] == $request->curlHandle) {
                            $response = $request->makeResponse(true);
                            $func = $request->endCallback();
                            $func($response);
                            break;
                        }
                    }
                }
            }
            usleep($sleepTime);
        } while ($running > 0);
        $return = array();
        foreach (self::$requestPool as $request) {
            $return[] = $request->makeResponse(true);
            curl_multi_remove_handle(self::$multiHandler, $request->curlHandle);
            curl_close($request->curlHandle);
        }
        curl_multi_close(self::$multiHandler);
        return $return;
    }

}

