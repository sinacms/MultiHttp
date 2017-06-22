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

class MultiRequest {
	protected static $requestPool;
	protected static $multiHandler;

	protected function __construct() {
		self::$multiHandler = curl_multi_init();
	}

	private static $instance;

	public static function create() {
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
	public function addOptions(array $URLOptions) {
		foreach ($URLOptions as $options) {
			$request = Request::create()->addOptions($options)->applyOptions();
			if (isset($options['callback'])) {
				$request->onEnd($options['callback']);
			}
			$this->import($request);
		}
		return $this;
	}

	public function add($method, $uri, array $payload = array(), array $options = array()) {
		$options = array(
			'method' => $method,
			'url'    => $uri,
			'data'   => $payload,
		)+$options;
		$this->addOptions(array($options));
		return $this;
	}

	public function import(Request $request) {
		if (!is_resource($request->curlHandle)) {throw new InvalidArgumentException('Request curl handle is not initialized');
		}

		curl_multi_add_handle(self::$multiHandler, $request->curlHandle);
		self::$requestPool[] = $request;
		return $this;
	}

	/**
	 * @return array(Response)
	 */
	public function execute() {
		$sleepTime = 1000;//microsecond, prevent  CPU 100%
		while (($multiFlg = curl_multi_exec(self::$multiHandler, $active)) == CURLM_CALL_MULTI_PERFORM);

		while ($active && $multiFlg == CURLM_OK) {
			// Wait for activity on any curl-connection
			while (curl_multi_exec(self::$multiHandler, $active) === CURLM_CALL_MULTI_PERFORM);
			// bug in PHP 5.3.18+ where curl_multi_select can return -1
			// https://bugs.php.net/bug.php?id=63411
			if (($f = curl_multi_select(self::$multiHandler)) === -1) {
				usleep($sleepTime);
			}
			/*
			if (false !== ($info = curl_multi_info_read(self::$multiHandler))) {
			if (isset($info['handle']) && is_resource($info['handle'])) {
			foreach (self::$requestPool as $request) {
			if ($request->hasEndCallback() && $info['handle'] == $request->curlHandle) {
			$response = $request->makeResponse(true);
			$func     = $request->endCallback();
			$func($response);
			break;
			}
			}
			}
			}
			 */
			usleep($sleepTime);
		}
		$return = array();
		foreach (self::$requestPool as $request) {
			$response = $request->makeResponse(true);
			curl_multi_remove_handle(self::$multiHandler, $request->curlHandle);
            curl_close($request->curlHandle);
            $func = $response->request->endCallback();
			if (isset($func)) {
				$func($response);
			}
			$return[] = $response;
		}
		curl_multi_close(self::$multiHandler);
		return $return;
	}

}
