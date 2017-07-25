<?php

namespace MultiCurlTest;

use MultiHttp\Http;
use MultiHttp\MultiRequest;
use MultiHttp\Request;
use MultiHttp\Response;

/**
 *
 * @author  qiangjian@staff.sina.com.cn sunsky303@gmail.com
 * Date: 2017/6/15
 * Time: 18:31
 * @version $Id: $
 * @since 1.0
 * @copyright Sina Corp.
 */
class MultiRequestTest extends \PHPUnit_Framework_TestCase
{
    public static function setupBeforeClass()
    {
    }

    function test()
    {
        $start = microtime(1);
        $mc = MultiRequest::create();
        $rtn = $mc->setDefaults(array(
            'timeout' => 2
        ))->addOptions(
            array(
                array(
                    'url' => TEST_SERVER . '/dynamic/blocking.php?&1',
                    'method' => 'HEAD',
                    'data' => array(),
                    'callback' => function (Response $response) {
                        //test setDefaults
                        self::assertEquals(2, $response->request->getIni('timeout'));
                        self::assertEmpty($response->body);

                        self::assertTrue(is_array($response->header) && sizeof($response->header)>0);
                        self::assertLessThan(1.5, $response->duration);
                        self::assertFalse($response->hasErrors(), $response->request->uri . $response->error);
                        self::assertEquals(TEST_SERVER . '/dynamic/blocking.php?&1', $response->request->uri);
                        self::assertEquals(Request::HEAD, $response->request->getIni('method'));
                        self::assertTrue($response->request->hasEndCallback());
                    }
                ),

                array(
                    'url' => TEST_SERVER . '/dynamic/blocking.php?sleepSecs=1&a',
                    'data' => array(
                        'data' => 'this_is_post_data',
                    ),
                    'callback' => function (Response $response) {
                        //test json
                        self::assertEquals(true, strlen($response->body)>0);
                        self::assertJson($response->body);
                        //test setDefaults
                        self::assertEquals(2, $response->request->getIni('timeout'));

                        self::assertFalse($response->hasErrors(), $response->request->uri . $response->error);
                        self::assertEquals(Request::GET, $response->request->getIni('method'));
                        self::assertTrue($response->request->hasEndCallback());
                        self::assertContains('this_is_post_data', $response->body);
                    }
                ),
                array(
                    'url' => TEST_SERVER . '/dynamic/blocking.php?&b',
                    'method' => 'POST',
                    'data' => array(
                        'data' => 'this_is_post_data',
                    ),
                    'callback' => function (Response $response) {
                        //test json
                        self::assertEquals(true, strlen($response->body)>0);
                        self::assertJson($response->body);
                        //test setDefaults
                        self::assertEquals(2, $response->request->getIni('timeout'));

                        self::assertFalse($response->hasErrors(), $response->request->uri . $response->error);
                        self::assertEquals(TEST_SERVER . '/dynamic/blocking.php?&b', $response->request->uri);
                        self::assertEquals(Request::POST, $response->request->getIni('method'));
                        self::assertTrue($response->request->hasEndCallback());
                        self::assertContains('this_is_post_data', $response->body);
                    }
                ),
                array(
                    'url' => TEST_SERVER . '/static/test.json',
                    'callback' => function (Response $response) {
                        self::assertFalse($response->hasErrors(), $response->request->uri . $response->error);
                        self::assertEquals(TEST_SERVER . '/static/test.json', $response->request->uri);
                        self::assertTrue(strlen($response->body) > 0);
                        self::assertJsonStringEqualsJsonFile(WEB_SERVER_DOCROOT . '/static/test.json', $response->body);
                    }
                ),
                array(
                    'url' => 'http://www.qq.com',
                    'timeout' => 3,
                    'callback' => function (Response $response) {
                        //test json
                        self::assertEquals(true, strlen($response->body)>0);
                        //test setDefaults
                        self::assertEquals(3, $response->request->getIni('timeout'));

                        self::assertContains('http://www.qq.com', $response->request->uri);
                        self::assertTrue($response->request->hasEndCallback());
                    },
                ),
                array(
                    'url' => 'http://proxy.test/dynamic/blocking.php',
                    'ip' => WEB_SERVER_HOST ,
                    'port' => WEB_SERVER_PORT,
                    'timeout' => 0,//unlimited timeout
                    'callback' => function (Response $response) {
                        self::assertFalse($response->hasErrors());
                        self::assertTrue(strlen($response->body) > 0);
                    }
                ),
                array(
                    'url' => TEST_SERVER.'/dynamic/blocking.php',
                    'expectsMime' => 'json',
                    'sendMime' => 'json',
                    'method' => 'POST',
                    'data' => array('aaa'=>'bbc'),
                    'timeout' => 0,//unlimited timeout
                    'callback' => function (Response $response) {
                        self::assertFalse($response->hasErrors());
                        self::assertEquals('{"aaa":"bbc"}', $response->request->getIni('data'));
                        self::assertEquals('{"aaa":"bbc"}', $response->body['postRaw']);
                        self::assertTrue(is_array($response->body) && sizeof($response->body));
                    }
                ),
                array(
                    'url' => TEST_SERVER.'/dynamic/blocking.php',
//                    'expectsMime' => 'json',
//                    'sendMime' => 'json',
                    'method' => 'POST',
                    'data' => array('aaa'=>'bbc'),
                    'timeout' => 0,//unlimited timeout
                    'callback' => function (Response $response) {
                        self::assertFalse($response->hasErrors());
                        self::assertTrue(is_string($response->body) && strlen($response->body));
                    }
                ),
            ))
            ->add(Http::GET, 'http://www.163.com', array(), array(
                'timeout' => 3,
                'callback' => function (Response $response) {
                    self::assertContains('http://www.163.com', $response->request->uri);
                },
            ))
            ->add('GET', 'http://sina.cn', array(), array(
                'timeout' => 3,
            ))
            ->import(Request::create()->trace('http://sohu.cn', array(
                'timeout' => 3,
                'callback' => function (Response $response) {
                    self::assertContains('http://sohu.cn', $response->request->uri);
                }))->applyOptions())
            ->import(Request::create()->options('http://toutiao.com', array(
                'timeout' => 3,
                'callback' => function (Response $response) {
                    self::assertContains('http://toutiao.com', $response->request->uri);
                }))->applyOptions())
            ->execute();
        echo "exec done\n\t";
        foreach ($rtn as $response) {
            echo $response->request->uri, ' takes:', $response->duration, ' ', "\n\t\n\t";
        }
        $end = microtime(1);
        echo 'multi total takes:', $end - $start, ' secs;';
        self::assertTrue($end - $start < 5);
    }

    protected function setUp()
    {
        parent::setUp();
        echo 'enter ' . __CLASS__ . PHP_EOL;
    }

}
