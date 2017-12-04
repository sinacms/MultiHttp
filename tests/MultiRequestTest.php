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
                    'url' => TEST_SERVER . '/dynamic/all_data.php?&a=1',
                    'method' => 'HEAD',
                    'data' => array(),
                    'callback' => function (Response $response) {
                        //test setDefaults
                        self::assertEquals(2, $response->request->getIni('timeout'));
                        self::assertEmpty($response->body);

                        self::assertTrue(is_array($response->header) && sizeof($response->header)>0);
                        self::assertLessThan(1.5, $response->duration);
                        self::assertFalse($response->hasErrors(), $response->request->uri . $response->error);
                        self::assertEquals(TEST_SERVER . '/dynamic/all_data.php?&a=1', $response->request->uri);
                        self::assertEquals(Request::HEAD, $response->request->getIni('method'));
                        self::assertTrue($response->request->hasEndCallback());
                    }
                ),

                array(
                    'url' => TEST_SERVER . '/dynamic/all_data.php?&a&data=this_is_post_data',
                    'data' => array(
                    ),
                    'expects_mime' => 'json',
                    'callback' => function (Response $response) {
                        //test json
                        self::assertNotEmpty($response->body);
                        //test setDefaults
                        self::assertEquals(2, $response->request->getIni('timeout'));

                        self::assertFalse($response->hasErrors(), $response->request->uri . $response->error);
                        self::assertEquals(Request::GET, $response->request->getIni('method'));
                        self::assertTrue($response->request->hasEndCallback());
                        self::assertContains('this_is_post_data', $response->body['g']['data']);
                    }
                ),
                array(
                    'url' => TEST_SERVER . '/dynamic/all_data.php?&b',
                    'method' => 'POST',
                    'expects_mime' => 'json',
                    'data' => array(
                        'data' => 'this_is_post_data',
                    ),
                    'callback' => function (Response $response) {
                        //test json
                        self::assertNotEmpty($response->body);
                        //test setDefaults
                        self::assertEquals(2, $response->request->getIni('timeout'));

                        self::assertFalse($response->hasErrors(), $response->request->uri . $response->error);
                        self::assertEquals(TEST_SERVER . '/dynamic/all_data.php?&b', $response->request->uri);
                        self::assertEquals(Request::POST, $response->request->getIni('method'));
                        self::assertTrue($response->request->hasEndCallback());
                        self::assertContains('this_is_post_data', $response->body['p']['data']);
                    }
                ),
                array(
                    'url' => TEST_SERVER . '/static/test.json',
                    'callback' => function (Response $response) {
                        self::assertFalse($response->hasErrors(), $response->request->uri . $response->error);
                        self::assertEquals(TEST_SERVER . '/static/test.json', $response->request->uri);
                        self::assertNotEmpty($response->body);
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
                    'url' => 'http://proxy.test/dynamic/all_data.php',
                    'ip' => WEB_SERVER_HOST ,
                    'port' => WEB_SERVER_PORT,
                    'timeout' => 0,//unlimited timeout
                    'callback' => function (Response $response) {
                        self::assertFalse($response->hasErrors());
                        self::assertNotEmpty($response->body);
                    }
                ),
                array(
                    'url' => TEST_SERVER.'/dynamic/all_data.php',
                    'expects_mime' => 'json',
                    'send_mime' => 'json',
                    'method' => 'POST',
                    'data' => array('aaa'=>'bbc2'),
                    'timeout' => 0,//unlimited timeout
                    'callback' => function (Response $response) {
                        var_dump($response->body);
                        self::assertFalse($response->hasErrors());
                        self::assertEquals(array('aaa'=>'bbc2'), $response->request->getIni('data'));
                        self::assertEquals('{"aaa":"bbc2"}', $response->body['postRaw']);
                        self::assertEquals(array('{"aaa":"bbc2"}' => ''), $response->body['p']);
                        self::assertTrue(is_array($response->body) && sizeof($response->body));
                    }
                ),
                array(
                    'url' => TEST_SERVER.'/dynamic/all_data.php',
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
            ->sendAll();
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
    }
}
