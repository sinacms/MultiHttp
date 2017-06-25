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

class Request extends Http {
	protected static $curlAlias = array(
		'url'             => 'CURLOPT_URL',
		'debug'           => 'CURLOPT_VERBOSE',//for debug verbose
		'method'          => 'CURLOPT_CUSTOMREQUEST',
		'data'            => 'CURLOPT_POSTFIELDS', // array or string , file begin with '@'
		'ua'              => 'CURLOPT_USERAGENT',
		'timeout'         => 'CURLOPT_TIMEOUT', // (secs) 0 means indefinitely
		'connect_timeout' => 'CURLOPT_CONNECTTIMEOUT',
		'referer'         => 'CURLOPT_REFERER',
		'binary'          => 'CURLOPT_BINARYTRANSFER',
		'port'            => 'CURLOPT_PORT',
		'header'          => 'CURLOPT_HEADER', // TRUE:include header
		'headers'         => 'CURLOPT_HTTPHEADER', // array
		'download'        => 'CURLOPT_FILE', // writing file stream (using fopen()), default is STDOUT
		'upload'          => 'CURLOPT_INFILE', // reading file stream
		'transfer'        => 'CURLOPT_RETURNTRANSFER', // TRUE:return string; FALSE:output directly (curl_exec)
		'follow_location' => 'CURLOPT_FOLLOWLOCATION',
		'timeout_ms'      => 'CURLOPT_TIMEOUT_MS', // milliseconds,  libcurl version > 7.36.0 ,
	);
	public $curlHandle;
    protected $options = array(
        'CURLOPT_MAXREDIRS' => 10,
//        'CURLOPT_IPRESOLVE' => CURL_IPRESOLVE_V4,//IPv4
        'header' => true,
        'method' => self::GET,
        'transfer' => true,
        'follow_location' => true,
        'timeout' => 0);
    protected $endCallback;
	protected $withURIQuery;

	protected function __construct() {

	}

	public static function create() {
		return new self;
	}

	public function endCallback() {
		return $this->endCallback;
	}

	public function hasEndCallback() {
		return isset($this->endCallback);
	}

	public function onEnd(callable$callback) {
		if (!is_callable($callback)) {throw new InvalidArgumentException('callback not is callable :'.print_r(callback, 1));
		}

		$this->endCallback = $callback;
		return $this;
	}

	public function getURI() {
		return $this->getIni('url');
	}

	/**
	 * @param $field alias or field name
	 * @return bool|mixed
	 */
	public function getIni($field) {
		$alias = self::optionAlias($field);
		if (null === ($rawField = constant($alias))) {throw new InvalidArgumentException('field is invalid');
		}

		return isset($this->options[$rawField])?$this->options[$rawField]:false;
	}


	public function addQuery($data) {
		if (!empty($data)) {
			if (is_array($data)) {
				$this->withURIQuery = http_build_query($data);
			} else if (is_string($data)) {
				$this->withURIQuery = $data;
			} else {
				throw new InvalidArgumentException('data must be array or string');
			}
		}
		return $this;
	}

	public function post($uri, array $payload = array(), array $options = array()) {
		return $this->ini(Http::POST, $uri, $payload, $options);
	}

	protected function ini($method, $url, array $data = array(), array $options = array()) {
		$options = array('url' => $url, 'method' => $method, 'data' => $data)+$options;
		$this->addOptions($options);

		return $this;
	}

	public function addOptions(array $options = array()) {
		$this->options = $options+$this->options;
		if (empty($this->options['url'])) {throw new InvalidArgumentException('url can not empty');
		}

		if (isset($this->options['data'])) {
			$this->options['data'] = is_array($this->options['data'])?http_build_query($this->options['data']):$this->options['data'];//for better compatibility
		}
		if (isset($this->withURIQuery)) {
			$this->options['url'] .= strpos($this->options['url'], '?') === FALSE?'?':'&';
			$this->options['url'] .= $this->withURIQuery;
		}
		if (isset($this->options['callback'])) {
			$this->onEnd($this->options['callback']);
			unset($this->options['callback']);
		}

		return $this;
	}

	/*  no body  */

	function put($uri, array $payload = array(), array $options = array()) {
		return $this->ini(Http::PUT, $uri, $payload, $options);
	}

	function patch($uri, array $payload = array(), array $options = array()) {
		return $this->ini(Http::PATCH, $uri, $payload, $options);
	}

	public function get($uri, array $options = array()) {
		return $this->ini(Http::GET, $uri, array(), $options);
	}

	function options($uri, array $options = array()) {
		return $this->ini(Http::OPTIONS, $uri, array(), $options);
	}

	function head($uri, array $options = array()) {
		return $this->ini(Http::HEAD, $uri, array('CURLOPT_NOBODY' => true), $options);
	}

	function delete($uri, array $options = array()) {
		return $this->ini(Http::DELETE, $uri, array(), $options);
	}

	function trace($uri, array $options = array()) {
		return $this->ini(Http::TRACE, $uri, array(), $options);
	}

	/**
	 * @return Response
	 */
	public function execute() {
		$this->applyOptions();
		$response = $this->makeResponse();
		if ($this->endCallback) {
			$func = $this->endCallback;
			$func($response);
		}
		return $response;
	}

	public function applyOptions() {
		$curl             = curl_init();
		$this->curlHandle = $curl;
		$this->prepare();
		return $this;
	}

	protected function prepare() {
		//swap ip and host
		if (!empty($this->options['ip'])) {
			$matches = array();
			preg_match('/\/\/([^\/]+)/', $this->options['url'], $matches);
			$host = $matches[1];
			if (empty($this->options['headers']) || !is_array($this->options['headers'])) {
				$this->options['headers'] = array('Host: '.$host);
			} else {
				$this->options['headers'][] = 'Host: '.$host;
			}
			$this->options['url'] = preg_replace('/\/\/([^\/]+)/', '//'.$this->options['ip'], $this->options['url']);
			unset($this->options['ip']);
			unset($host);
		}
		//process version
		if (!empty($this->options['http_version'])) {
			$version                                                             = $this->options['http_version'];
			if ($version == '1.0') {$this->options['CURLOPT_HTTP_VERSION']       = CURLOPT_HTTP_VERSION_1_0;
			} elseif ($version == '1.1') {$this->options['CURLOPT_HTTP_VERSION'] = CURLOPT_HTTP_VERSION_1_1;
			}

			unset($version);
		}

		//convert secs to milliseconds
		if (defined('CURLOPT_TIMEOUT_MS')) {
			if (!isset($this->options['timeout_ms'])) {
				$this->options['timeout_ms'] = intval($this->options['timeout']*1000);
			} else {
				$this->options['timeout_ms'] = intval($this->options['timeout_ms']);
			}
		}

		self::filterAndRaw($this->options);

        curl_setopt_array($this->curlHandle, $this->options);

		return $this;
	}

	protected static function filterAndRaw(array&$options) {
		$opts = array();
		foreach ($options as $key => $val) {
			$alias = self::optionAlias($key);
			unset($options[$key]);
			if ($alias) {$opts[constant($alias)] = $val;
			}
		}
		$options = $opts;
	}

	/**
	 * @param $key
	 * @return mixed
	 */
	protected static function optionAlias($key) {
		$alias = false;
		if (isset(self::$curlAlias[$key])) {
			$alias = self::$curlAlias[$key];
		} elseif ((substr($key, 0, strlen('CURLOPT_')) == 'CURLOPT_') && defined($key)) {
			$alias = $key;
		}
		return $alias;
	}
	public function makeResponse($isMultiCurl = false) {
		$body     = $isMultiCurl?curl_multi_getcontent($this->curlHandle):curl_exec($this->curlHandle);
		$info     = curl_getinfo($this->curlHandle);
		$errno    = curl_errno($this->curlHandle);
		$error    = curl_error($this->curlHandle);
		$response = Response::create($this, $body, $info, $errno, $error);
		return $response;
	}
}
