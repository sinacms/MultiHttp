<?php
/**
 *
 * @author  qiangjian@staff.sina.com.cn sunsky303@gmail.com
 * Date: 2017/6/16
 * Time: 10:02
 * @version $Id: $
 * @since 1.0
 * @copyright Sina Corp.
 */

namespace MultiHttp;

use MultiHttp\Exception\InvalidArgumentException;
use MultiHttp\Exception\InvalidOperationException;

/**
 * Class Request
 * @package MultiHttp
 */
class Request extends Http
{
    /**
     * you can implement more traits
     */
    use JsonTrait;

    protected static $curlAlias = array(
        'url' => 'CURLOPT_URL',
        'uri' => 'CURLOPT_URL',
        'debug' => 'CURLOPT_VERBOSE',//for debug verbose
        'method' => 'CURLOPT_CUSTOMREQUEST',
        'data' => 'CURLOPT_POSTFIELDS', // array or string , file begin with '@'
        'ua' => 'CURLOPT_USERAGENT',
        'timeout' => 'CURLOPT_TIMEOUT', // (secs) 0 means indefinitely
        'connect_timeout' => 'CURLOPT_CONNECTTIMEOUT',
        'referer' => 'CURLOPT_REFERER',
        'binary' => 'CURLOPT_BINARYTRANSFER',
        'port' => 'CURLOPT_PORT',
        'header' => 'CURLOPT_HEADER', // TRUE:include header
        'headers' => 'CURLOPT_HTTPHEADER', // array
        'download' => 'CURLOPT_FILE', // writing file stream (using fopen()), default is STDOUT
        'upload' => 'CURLOPT_INFILE', // reading file stream
        'transfer' => 'CURLOPT_RETURNTRANSFER', // TRUE:return string; FALSE:output directly (curl_exec)
        'follow_location' => 'CURLOPT_FOLLOWLOCATION',
        'timeout_ms' => 'CURLOPT_TIMEOUT_MS', // milliseconds,  libcurl version > 7.36.0 ,
        'expects_mime' => null, //expected mime
        'send_mime' => null, //send mime
        'ip' => null,//specify ip to send request
        'callback' => null,//callback on end

    );
    protected static $loggerHandler;
    public
        $curlHandle,
        $uri,
        $sendMime,
        $expectedMime,
        $timeout,
        $maxRedirects,
        $encoding,
        $payload,
        $retryTimes,
        /**
         * @var int seconds
         */
        $retryDuration,
        $followRedirects;

    protected
        $body,
        $endCallback,
        $withURIQuery,
        $hasInitialized = false,
        /**
         * @var array
         */
        $options = array(
            'CURLOPT_MAXREDIRS' => 10,
            'CURLOPT_SSL_VERIFYPEER' => false,//for https
            'CURLOPT_SSL_VERIFYHOST' => 0,//for https
            'CURLOPT_IPRESOLVE' => CURL_IPRESOLVE_V4,//ipv4 first
//            'CURLOPT_SAFE_UPLOAD' => false,// compatible with PHP 5.6.0
            'header' => true,
            'method' => self::GET,
            'transfer' => true,
            'headers' => array(),
            'follow_location' => true,
            'timeout' => 0,
            //        'ip' => null, //host, in string, .e.g: 172.16.1.1:888
            'retry_times' => 1,//redo task when failed
            'retry_duration' => 0,//in seconds
        );


    /**
     * Request constructor.
     */
    protected function __construct()
    {
    }

    /**
     * @return Request
     */
    public static function create()
    {
        return new self;
    }

    /**
     * @param callable $handler
     */
    public static function setLogHandler(callable $handler)
    {
        self::$loggerHandler = $handler;
    }
    /**
     * Specify   timeout
     * @param float|int $timeout seconds to timeout the HTTP call
     * @return Request
     */
    public function timeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @return Request
     */
    public function noFollow()
    {
        return $this->follow(0);
    }

    /**
     * If the response is a 301 or 302 redirect, automatically
     * send off another request to that location
     * @param int $follow follow or not to follow or maximal number of redirects
     * @return Request
     */
    public function follow($follow)
    {
        $this->maxRedirects = abs($follow);
        $this->followRedirects = $follow > 0;
        return $this;
    }

    /**
     * @param $parsedComponents
     * @return string
     */
    private static function combineUrl($parsedComponents)
    {
        $scheme = isset($parsedComponents['scheme']) ? $parsedComponents['scheme'] . '://' : '';
        $host = isset($parsedComponents['host']) ? $parsedComponents['host'] : '';
        $port = isset($parsedComponents['port']) ? ':' . $parsedComponents['port'] : '';
        $user = isset($parsedComponents['user']) ? $parsedComponents['user'] : '';
        $pass = isset($parsedComponents['pass']) ? ':' . $parsedComponents['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsedComponents['path']) ? $parsedComponents['path'] : '';
        $query = isset($parsedComponents['query']) ? '?' . $parsedComponents['query'] : '';
        $fragment = isset($parsedComponents['fragment']) ? '#' . $parsedComponents['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    /**
     * @param string $mime
     * @return $this
     */
    public function expectsMime($mime = 'json')
    {
        $this->expectedMime = $mime;
        return $this;
    }

    /**
     * @param string $mime
     * @return Request
     */
    public function sendMime($mime = 'json')
    {
        $this->sendMime = $mime;
//        $this->addHeader('Content-type', Mime::getFullMime($mime));
        return $this;
    }

    /**
     * @param $headerName
     * @param $value , can be rawurlencode
     * @return $this
     */
    public function addHeader($headerName, $value)
    {
        $this->options['headers'][] = $headerName . ': ' . $value;
        return $this;
    }

    /**
     * @param $uri
     * @return $this
     */
    public function uri($uri)
    {
        $this->uri = $uri;
        return $this;
    }



    /**
     * @param array $headers
     * @return $this
     */
    public function addHeaders(array $headers)
    {
        foreach ($headers as $header => $value) {
            $this->addHeader($header, $value);
        }
        return $this;
    }
    public function expectsType($mime)
    {
        return $this->expects($mime);
    }
    public function sendType($mime)
    {
        return $this->contentType = $mime;
    }
    public function expects($mime)
    {
        if (empty($mime)) return $this;
        $this->expected_type = Mime::getFullMime($mime);
        return $this;
    }

    /**
     * @return mixed
     */
    public function endCallback()
    {
        return $this->endCallback;
    }

    /**
     * @return bool
     */
    public function hasEndCallback()
    {
        return isset($this->endCallback);
    }

    /**
     * @param $field alias or field name
     * @return bool|mixed
     */
    public function getIni($field = null)
    {
        if(!$field) return $this->options;
        $full = self::fullOption($field);
        return isset($this->options[$full]) ? $this->options[$full] : false;
    }

    /**
     * @param $key
     * @return mixed
     */
    protected static function fullOption($key)
    {
        $full = false;
        if (isset(self::$curlAlias[$key])) {
            $full = self::$curlAlias[$key];
        } elseif ((substr($key, 0, strlen('CURLOPT_')) == 'CURLOPT_') && defined($key)) {
            $full = $key;
        }
        return $full;
    }

    /**
     * @param $queryData
     * @return $this
     */
    public function addQuery($queryData)
    {
        if (!empty($queryData)) {
            if (is_array($queryData)) {
                $this->withURIQuery = http_build_query($queryData);
            } else if (is_string($queryData)) {
                $this->withURIQuery = $queryData;
            } else {
                throw new InvalidArgumentException('data must be array or string');
            }
        }
        return $this;
    }
    /**
     * @param $uri
     * @param null $payload
     * @param array $options
     * @return Request
     */
    public function post($uri, $payload = null, array $options = array())
    {
        return $this->ini(Http::POST, $uri, $payload, $options);
    }

    /**
     * @param $uri
     * @param null $payload
     * @param array $options
     * @param null $response
     * @return string
     */
    public function quickPost($uri, $payload = null, array $options = array(), &$response = null)
    {
        $response = $this->post($uri, $payload, $options)->send();
        return $response->body;
    }


    /**
     * @param $method
     * @param $url
     * @param $data
     * @param array $options
     * @return $this
     */
    protected function ini($method, $url, $data, array $options = array())
    {
        $options = array('url' => $url, 'method' => $method, 'data' => $data) + $options;
        $this->addOptions($options);

        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function addOptions(array $options = array())
    {
        $this->options = $options + $this->options;
        $this->uri = $this->options['url'];
        return $this;
    }

    /**
     * @param $uri
     * @param null $payload
     * @param array $options
     * @return Request
     */
    function put($uri, $payload = null, array $options = array())
    {
        return $this->ini(Http::PUT, $uri, $payload, $options);
    }

    /**
     * @param $uri
     * @param null $payload
     * @param array $options
     * @return Request
     */
    function patch($uri, $payload = null, array $options = array())
    {
        return $this->ini(Http::PATCH, $uri, $payload, $options);
    }

    /**
     * @param $uri
     * @param array $options
     * @return Request
     */
    public function get($uri, array $options = array())
    {
        return $this->ini(Http::GET, $uri, array(), $options);
    }


    /**
     * @param $uri
     * @param array $options
     * @param null $response
     * @return string
     */
    public function quickGet($uri, array $options = array(), &$response = null)
    {
        $response = $this->get($uri, $options)->send();
        return $response->body;
    }

    /**
     * @param $uri
     * @param array $options
     * @return Request
     */
    function options($uri, array $options = array())
    {
        return $this->ini(Http::OPTIONS, $uri, array(), $options);
    }

    /**
     * @param $uri
     * @param array $options
     * @return Request
     */
    function head($uri, array $options = array())
    {
        return $this->ini(Http::HEAD, $uri, array('CURLOPT_NOBODY' => true), $options);
    }

    /**
     * @param $uri
     * @param array $options
     * @return Request
     */
    function delete($uri, array $options = array())
    {
        return $this->ini(Http::DELETE, $uri, array(), $options);
    }

    /**
     * @param $uri
     * @param array $options
     * @return Request
     */
    function trace($uri, array $options = array())
    {
        return $this->ini(Http::TRACE, $uri, array(), $options);
    }

    /**
     * @param bool $isMultiCurl
     * @return Response
     */
    public function send($isMultiCurl = false)
    {
        try {
            if (!$this->hasInitialized)
                $this->applyOptions();
            $response = $this->makeResponse($isMultiCurl);
            $response->parse();
        } catch (\Exception $e) {
            if(!isset($response)) $response = Response::create($this, null, null, null, null);
            $response->error = $e->getMessage();
            $response->errorCode = 999;
        }

        if (self::$loggerHandler) {
            call_user_func(self::$loggerHandler, $response);
        }
        if ($this->endCallback) {
            call_user_func($this->endCallback, $response);
        }

        return $response;
    }

    /**
     * @return $this
     */
    public function applyOptions()
    {
        $curl = curl_init();
        $this->curlHandle = $curl;
        $this->prepare();
        $this->hasInitialized = true;
        return $this;
    }

    /**
     * @return $this
     */
    protected function prepare()
    {
        $this->options['url'] = trim($this->options['url']);
        if (empty($this->options['url'])) {
            throw new InvalidArgumentException('url can not empty');
        }

        if (isset($this->options['retry_times'])) {
            $this->retryTimes = abs($this->options['retry_times']);
        }

        if (isset($this->options['retry_duration'])) {
            $this->retryDuration = abs($this->options['retry_duration']);
        }

        if(isset($this->options['expects_mime'])){
            $this->expectsMime($this->options['expects_mime']);
        }

        if(isset($this->options['send_mime'])){
            $this->sendMime($this->options['send_mime']);
        }

//        if(!empty($this->options['data']) && !Http::hasBody($this->options['method'])){
//            $this->withURIQuery =  is_array($this->options['data']) ? http_build_query($this->options['data']) : $this->options['data'];
//        }
        if (isset($this->withURIQuery)) {
            $this->options['url'] .= strpos($this->options['url'], '?') === FALSE ? '?' : '&';
            $this->options['url'] .= $this->withURIQuery;
        }

        $this->serializeBody();

        //try fix url
        if (strpos($this->options['url'], '://') === FALSE) $this->options['url'] = 'http://' . $this->options['url'];
        $components = parse_url($this->options['url']);
        if(FALSE === $components) throw new InvalidArgumentException('formatting url occurs error: '. $this->options['url']);
        if($this->withURIQuery){
            if(isset($components['query'])) $components['query'] .= '&'. trim($this->withURIQuery);
            else $components['query'] = trim($this->withURIQuery);
        }
        $this->options['url'] = self::combineUrl($components);

        if (isset($this->options['callback'])) {
            $this->onEnd($this->options['callback']);
        }
        //swap ip and host
        if (!empty($this->options['ip'])) {
            $matches = array();
            preg_match('/\/\/([^\/]+)/', $this->options['url'], $matches);
            $host = $matches[1];
            if (empty($this->options['headers']) || !is_array($this->options['headers'])) {
                $this->options['headers'] = array('Host: ' . $host);
            } else {
                $this->options['headers'][] = 'Host: ' . $host;
            }
            $this->options['url'] = str_replace("//{$host}", '//' . $this->options['ip'], $this->options['url']);
            unset($host);
        }
        //process version
        if (!empty($this->options['http_version'])) {
            $version = $this->options['http_version'];
            if ($version == '1.0') {
                $this->options['CURLOPT_HTTP_VERSION'] = CURLOPT_HTTP_VERSION_1_0;
            } elseif ($version == '1.1') {
                $this->options['CURLOPT_HTTP_VERSION'] = CURLOPT_HTTP_VERSION_1_1;
            }

            unset($version);
        }

        //convert secs to milliseconds
        if (defined('CURLOPT_TIMEOUT_MS')) {
            if (!isset($this->options['timeout_ms'])) {
                $this->options['timeout_ms'] = intval($this->options['timeout'] * 1000);
            } else {
                $this->options['timeout_ms'] = intval($this->options['timeout_ms']);
            }
        }

        $cURLOptions = self::filterAndRaw($this->options);
        if(isset($this->body))$cURLOptions[CURLOPT_POSTFIELDS] = $this->body;//use serialized body not raw data
        curl_setopt_array($this->curlHandle, $cURLOptions);

        return $this;
    }

    public function serializeBody()
    {
        if (isset($this->options['data'])) {
            if (isset($this->sendMime)) {
                $method = $this->sendMime;
                if (!method_exists($this, $method)) throw new InvalidOperationException($method . ' is not exists in ' . __CLASS__);
                $this->body = $this->$method($this->options['data']);
            } else {
                $this->body =  is_array($this->options['data']) ? http_build_query($this->options['data']) : $this->options['data'];
            }

        }
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function onEnd(callable $callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('callback not is callable :' . print_r($callback, 1));
        }

        $this->endCallback = $callback;
        return $this;
    }

    /**
     * @param array $options
     * @return array
     */
    protected static function filterAndRaw(array &$options)
    {
        $opts = $fullsOpts = array();
        foreach ($options as $key => $val) {
            $fullOption = self::fullOption($key);

            if ($fullOption) {
                $fullsOpts[$fullOption] = $val;
                $opts[constant($fullOption)] = $val;
            }
            unset($options[$key]);
        }
        $options = $fullsOpts;
        return $opts;
    }

    /**
     * @param bool $isMultiCurl
     * @return Response
     * @throws \Exception
     */
    public function makeResponse($isMultiCurl = false)
    {
        $handle = $this->curlHandle;
        $body = $errno = null;
        Helper::retry($this->retryTimes, function()use(&$body, &$errno, $isMultiCurl, $handle){
            $body = $isMultiCurl ? curl_multi_getcontent($handle) : curl_exec($handle);
            $errno = curl_errno($handle);
            return 0 == $errno;
        }, $this->retryDuration);

        $info = curl_getinfo($this->curlHandle);
        $errorCode = curl_errno($this->curlHandle);
        $error = curl_error($this->curlHandle);
        $response = Response::create($this, $body, $info, $errorCode, $error);
        return $response;
    }


}
