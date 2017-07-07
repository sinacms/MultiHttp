<?php
/**
 *
 * @author  qiangjian@staff.sina.com.cn sunsky303@gmail.com
 * Date: 2017/6/21
 * Time: 11:42
 * @version $Id: $
 * @since 1.0
 * @copyright Sina Corp.
 */
header('Content-type:application/json; charset=utf8');
header('X-CMS-INFO: test');
header('X-CMS-invalid');

$secs = isset($_GET['sleepSecs'])?$_GET['sleepSecs']:0;
sleep($secs);
echo json_encode([
		'method'    => $_SERVER['REQUEST_METHOD'],
		'sleepSecs' => $secs,
		'uri'       => $_SERVER['REQUEST_URI'],
		'post'      => $_POST,
		'get'       => $_GET,
	]);