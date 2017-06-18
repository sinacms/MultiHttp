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

namespace sinacms\MultiHttp;


class Curl
{

    protected $options = array('header' => false, 'transfer' => true, 'follow_location' => true);
    protected static $alias = array(
        'url' => 'CURLOPT_URL',
        'method' => 'CURLOPT_CUSTOMREQUEST',
        'data' => 'CURLOPT_POSTFIELDS', // array or string , file begin with '@'
        'ua' => 'CURLOPT_USERAGENT',
        'timeout' => 'CURLOPT_TIMEOUT',   // (secs) 0 means indefinitely
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
    );

    protected $return ;
    protected $httpInfo ;
    protected $errorCode ;
    protected $errorString ;
    protected $ch ;

    protected function __construct()
    {
    }

    public static function create(){
        return new self;
    }



    public function fetch()
    {
        return $this->return;
    }
    public function info()
    {
        return $this->httpInfo;
    }
    public function errorCode()
    {
        return $this->errorCode;
    }
    public function errorString()
    {
        return $this->errorString;
    }

    protected function prepare()
    {
        if (empty($this->options['url'])) throw new CurlInvalidArgumentException('url can not empty');

        //swap ip and host
        if (!empty($this->options['ip'])) {
            preg_match('/\/\/([^\/]+)/', $this->options['url'], $matches);
            $host = $matches[1];
            if (empty($this->options['headers']) || !is_array($this->options['headers'])) {
                $this->options['headers'] = array('Host: ' . $host);
            } else {
                $this->options['headers'][] = 'Host: ' . $host;
            }
            $this->options['url'] = preg_replace('/\/\/([^\/]+)/', '//' . $this->options['ip'], $this->options['url']);
            unset($this->options['ip']);
            unset($host);
        }
        //process version
        if (!empty($this->options['http_version'])) {
            $version = $this->options['http_version'];
            if ($version == '1.0') $this->options['CURLOPT_HTTP_VERSION'] = CURLOPT_HTTP_VERSION_1_0;
            elseif ($version == '1.1') $this->options['CURLOPT_HTTP_VERSION'] = CURLOPT_HTTP_VERSION_1_1;
            unset($version);
        }

        //convert secs to milliseconds
        if (!isset($this->options['timeout_ms'])) {
            $this->options['timeout_ms'] = intval($this->options['timeout'] * 1000);
        } else {
            $this->options['timeout_ms'] = intval($this->options['timeout_ms']);
        }

        self::filterAndRaw($this->options);

        return $this;
    }

    public function ini($method, $url, array $data = array(), array $options = array())
    {
        $this->options  = (array('url' => $url, 'method' => $method, 'data' => $data) + $options) + $this->options ;
        return $this;
    }

    protected static function filterAndRaw(array &$options)
    {
        foreach ($options as $key => $val) {
            if (isset(self::$alias[$key])) {
                $options[constant(self::$alias[$key])] = $val;
            } elseif ((substr($key, 0, strlen('CURLOPT_')) == 'CURLOPT_') && defined($key)) {
                $options[constant($key)] = $val;
            }
            unset($options[$key]);
        }
    }

    public function applyOptions(){
        $curl = curl_init();
        $this->prepare();
        curl_setopt_array($curl, $this->options);
        $this->ch = $curl;
        return $this;
    }
    public function execute()
    {
        $this->applyOptions();
        $this->return = curl_exec($this->ch);
        $this->httpInfo = curl_getinfo($this->ch);
        $this->errorCode = curl_errno($this->ch);
        $this->errorString = curl_error($this->ch);
        curl_close($this->ch);
        return $this;

    }


}