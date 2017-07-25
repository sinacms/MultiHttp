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
header('test1: from_server');

$secs = isset($_GET['sleepSecs'])?$_GET['sleepSecs']:0;
sleep($secs);
echo json_encode([
		'method'    => $_SERVER['REQUEST_METHOD'],
		'sleepSecs' => $secs,
		'uri'       => $_SERVER['REQUEST_URI'],
		'post'      => $_POST,
		'postRaw'      => file_get_contents("php://input"),
		'get'       => $_GET,
	]);