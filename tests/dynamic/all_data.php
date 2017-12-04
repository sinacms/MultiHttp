<?php
/**
 *
 * @author  qiangjian@staff.sina.com.cn sunsky303@gmail.com
 * Date: 2017/11/16
 * Time: 19:02
 * @version $Id: $
 * @since 1.0
 * @copyright Sina Corp.
 */
header('Content-type:application/json; charset=utf8');
header('test1: from_server');

$sleep = isset($_GET['sleep'])?$_GET['sleep']:0;
sleep($sleep);
echo json_encode([
    'sleep' => $sleep,
    'f' => $_FILES,
    'g' => $_GET,
    'p' => $_POST,
    's' => $_SERVER,
    'postRaw'      => file_get_contents("php://input"),
]);
