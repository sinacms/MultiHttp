<?php

namespace MultiCurlTest;

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
class RequestTest extends \PHPUnit_Framework_TestCase
{
    public static function setupBeforeClass()
    {
    }

    function test()
    {
        $start = microtime(1);
        $responses = array();
        Request::setLogHandler(function(Response $response){
            echo PHP_EOL.'LogHandler: '. $response->request->uri . " info: " . $response->error. PHP_EOL;
        });
        //test https get
        $responses[] = Request::create()->get('https://www.baidu.com/?q=cms&a=b#c=d', array(
            'timeout' => 5,
            'callback' => function (Response $response) {
                self::assertTrue(strlen($response->body) > 0);
            }
        ))->addQuery('e=f')->send();

        //test timeout/http get
        $responses[] = Request::create()->addQuery('sleepSecs=2')->get(TEST_SERVER . '/dynamic/blocking.php', array(
            'timeout' => 3,
            'timeout_ms' => 2000,
            'callback' => function (Response $response) {
                self::assertLessThan(3, $response->duration);
                self::assertGreaterThan(1, $response->duration);
                self::assertTrue($response->hasErrors(), $response->error);
                self::assertTrue(!$response->body);
                self::assertEquals(TEST_SERVER . '/dynamic/blocking.php', $response->request->uri);
                self::assertEquals(true,$response->request->hasEndCallback());
            }))->send();
        //test expectsJson/headers
        $responses[] = Request::create()->addQuery('sleepSecs=2')->get(TEST_SERVER . '/dynamic/blocking.php#1', array(
            'timeout' => 3,
            'callback' => function (Response $response) {
                self::assertLessThan(3, $response->duration);
                self::assertGreaterThan(2, $response->duration);
                self::assertTrue(!$response->hasErrors(), $response->error);
                self::assertTrue(is_array($response->body));
                self::assertTrue(sizeof($response->body)>0);
                self::assertEquals(TEST_SERVER . '/dynamic/blocking.php#1', $response->request->uri);
                self::assertEquals(true,$response->request->hasEndCallback());
            }))->sendJson()->expectsJson()->addHeader('Debug', 'addHeader')->addHeaders(array('Debug2'=> 'addHeaders哈"'))->send();

        //test trace
        $responses[] = Request::create()->addQuery(array('sleepSecs' => 2))->trace(TEST_SERVER . '/dynamic/blocking.php', array(
            'timeout' => 3,
            'callback' => function (Response $response) {
                self::assertTrue(strlen($response->body) > 0);
                self::assertFalse($response->hasErrors());
                self::assertJson($response->body);
                self::assertEquals(TEST_SERVER . '/dynamic/blocking.php', $response->request->uri);
                self::assertEquals(3, $response->request->getIni('timeout'));
            }))->send();
        //test put
        $responses[] = Request::create()->put(TEST_SERVER . '/static/test.json')->onEnd(function (Response $response) {
            self::assertFalse($response->hasErrors());
            self::assertEquals(TEST_SERVER . '/static/test.json', $response->request->uri);
            self::assertTrue(strlen($response->body) > 0);
            self::assertJsonStringEqualsJsonFile(WEB_SERVER_DOCROOT . '/static/test.json', $response->body);
        })->send();

        //test patch/addOptions
        $responses[] = Request::create()->get(TEST_SERVER . '/dynamic/blocking.php', array(
            'callback' => function (Response $response) {
                self::assertEquals(Request::PATCH, $response->request->getIni('method'));
                self::assertTrue($response->request->hasEndCallback());
            }))->addOptions(array(
            'method' => Request::PATCH
        ))->send();
        $responses[] = Request::create()->patch(TEST_SERVER . '/dynamic/blocking.php', array(
            'callback' => function (Response $response) {
                self::assertEquals(Request::PATCH, $response->request->getIni('method'));
                self::assertTrue($response->request->hasEndCallback());
            }))->send();

        //test post
        $responses[] = Request::create()->post(TEST_SERVER . '/dynamic/blocking.php', array('data' => 'this_is_post_data'), array(
            'callback' => function (Response $response) {
                self::assertEquals(Request::POST, $response->request->getIni('method'));
                self::assertTrue($response->request->hasEndCallback());
                self::assertContains('this_is_post_data', $response->body);

            }))->send();

        //test delete
        $responses[] = Request::create()->delete(TEST_SERVER . '/dynamic/blocking.php', array(), array(
            //            'data' => 'data=this_is_post_data', //not work
            'callback' => function (Response $response) {
                self::assertEquals(Request::DELETE, $response->request->getIni('method'));
                self::assertTrue($response->request->hasEndCallback());
                self::assertNotContains('this_is_post_data', $response->body);

            }))->send();

        //test proxy ip
        $responses[] = Request::create()->get('http://test-proxy.local/dynamic/blocking.php', array(
                'ip' => WEB_SERVER_HOST,
                'port' => WEB_SERVER_PORT,
                'timeout' => 2,
                'callback' => function (Response $response) {
                    self::assertFalse($response->hasErrors());
                    self::assertTrue(strlen($response->body) > 0);
                }
            ))->send();

        //test head
        $response = Request::create()->head(TEST_SERVER . '/dynamic/blocking.php?head', array(
            'callback' => function (Response $response) {
                self::assertEmpty($response->body);
            }
        ))->applyOptions()->send();
        self::assertInstanceOf('\MultiHttp\Response', $response);
        self::assertEmpty($response->body);
        self::assertNotEmpty($response->header);

        echo "\n\t\n\texec done\n\t\n\t";
        foreach ($responses as $response) {
            echo $response->request->uri, ' takes:', $response->duration, "\n\t\n\t";
        }
        $end = microtime(1);
        echo 'total takes:', $end - $start, ' secs;';

    }

    protected function setUp()
    {
        parent::setUp();
    }
}
