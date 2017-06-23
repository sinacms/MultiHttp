<?php
/**
 *
 * @author  qiangjian@staff.sina.com.cn sunsky303@gmail.com
 * Date: 2017/6/18
 * Time: 11:46
 * @version $Id: $
 * @since 1.0
 * @copyright Sina Corp.
 */
set_time_limit(0);
require_once __DIR__.'/bootstrap-server.php';
require_once __DIR__.'/../vendor/autoload.php';

$r = file_get_contents(TEST_SERVER.'/dynamic/blocking.php');
var_dump($r);

$r = \MultiHttp\Request::create()->get('http://www.google.com', ['timeout'=>2])->execute();
var_dump($r);



