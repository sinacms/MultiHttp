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
    protected function setUp()
    {
        parent::setUp();
    }
    function testHTTPS()
    {
        \MultiHttp\Request::create()->quickGet('https://github.com/',[
                'callback' => function($response){
                    $this->assertNotEmpty($response->body);
                }
        ]);
    }
    function testQuick()
    {
        $response = null;
        $uniqId = uniqid();
        \MultiHttp\Request::create()->quickGet(TEST_SERVER . '/dynamic/all_data.php' . '?aa=' . $uniqId, [
            'callback' => function (\MultiHttp\Response $response) use ($uniqId) {
                $this->assertInstanceOf('MultiHttp\Response', $response);
                $result = json_decode($response->body,1);
                $this->assertEquals($uniqId, $result['g']['aa'], "assert error \n");
            }
        ], $response);
        $uniqId = uniqid();
        \MultiHttp\Request::create()->quickPost(TEST_SERVER . '/dynamic/all_data.php?aa=AA',
            [
                'bb' => $uniqId
            ]
            , [
                'callback' => function (\MultiHttp\Response $response) use ($uniqId) {
                    $this->assertInstanceOf('MultiHttp\Response', $response);
                    $result = json_decode($response->body,1);
                    $this->assertEquals('AA', $result['g']['aa'], "assert error \n");
                    $this->assertEquals($uniqId, $result['p']['bb'], "assert error \n");
                }
            ], $response);
        $this->assertInstanceOf('MultiHttp\Response', $response);

        \MultiHttp\Request::create()->quickGet('http://google.com', [
            'timeout' => 1,
            'retry_times' => 3,
            'retry_duration' => 1,
            'callback' => function ($response) {
                $this->assertInstanceOf('MultiHttp\Response', $response);
            }
        ], $response);
        $this->assertInstanceOf('MultiHttp\Response', $response);

        echo "\n\n\n";
        \MultiHttp\Request::create()->quickPost('http://facebook.com', [], [
            'timeout' => 3,
            'retry_times' => 2,
            'retry_duration' => 1,
            'callback' => function ($response) {
                $this->assertInstanceOf('MultiHttp\Response', $response);
            }
        ], $response);
        $this->assertInstanceOf('MultiHttp\Response', $response);
    }

    function testLazy()
    {
        $mr = \MultiHttp\MultiRequest::create();
        $mr->add('GET', 'http://sina.cn', array(), array(
            'timeout' => 3,
            'callback'=> function($response){
                $this->assertInstanceOf('MultiHttp\Response', $response);
                $this->assertNotEmpty($response->body);
                //do sth with response
            }
        ));
        $mr->add('POST', TEST_SERVER.'/dynamic/all_data.php?sleep=1', array(), array(
            'timeout' => 3,
            'callback'=> function($response){
                $this->assertInstanceOf('MultiHttp\Response', $response);
                $this->assertNotEmpty($response->body);
                //do sth with response
            }
        ));
        //add ...
//        $mr->sendAll();
        $results = $mr->sendAll();
        echo 'size of results : '. sizeof($results)."\n";
    }

    function testRetry()
    {
        $i = 0;
        $trys = \MultiHttp\Helper::retry(5, function () use (&$i) {
            if (++$i > 3) {
                echo $i . "\n";
                ob_flush();
                flush();
                return true;
            } else {
                echo $i . "\n";
                ob_flush();
                flush();
                return false;
            }
        }, 1);
        echo 'retry times: ' . $trys . "\n";
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
        $responses[] = Request::create()->addQuery('sleep=2')->get(TEST_SERVER . '/dynamic/all_data.php', array(
            'timeout' => 3,
            'timeout_ms' => 2000,
            'callback' => function (Response $response) {
                self::assertLessThan(3, $response->duration);
                self::assertGreaterThan(1, $response->duration);
                self::assertTrue($response->hasErrors(), $response->error);
                self::assertTrue(!$response->body);
                self::assertEquals(TEST_SERVER . '/dynamic/all_data.php', $response->request->uri);
                self::assertEquals(true,$response->request->hasEndCallback());
            }))->send();
        //test expectsJson/headers
        $responses[] = Request::create()->addQuery('sleep=2')->get(TEST_SERVER . '/dynamic/all_data.php#1', array(
            'timeout' => 3,
            'callback' => function (Response $response) {
                self::assertLessThan(3, $response->duration);
                self::assertGreaterThan(2, $response->duration);
                self::assertTrue(!$response->hasErrors(), $response->error);
                self::assertTrue(is_array($response->body));
                self::assertTrue(sizeof($response->body)>0);
                self::assertEquals(TEST_SERVER . '/dynamic/all_data.php#1', $response->request->uri);
                self::assertEquals(true,$response->request->hasEndCallback());
            }))->sendMime('json')->expectsMime('json')->addHeader('Debug', 'addHeader')->addHeaders(array('Debug2'=> 'addHeaders哈"'))->send();

        //test trace
        $responses[] = Request::create()->addQuery(array('sleep' => 2))->trace(TEST_SERVER . '/dynamic/all_data.php', array(
            'timeout' => 3,
            'callback' => function (Response $response) {
                self::assertTrue(strlen($response->body) > 0);
                self::assertFalse($response->hasErrors());
                self::assertJson($response->body);
                self::assertEquals(TEST_SERVER . '/dynamic/all_data.php', $response->request->uri);
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
        $responses[] = Request::create()->get(TEST_SERVER . '/dynamic/all_data.php', array(
            'callback' => function (Response $response) {
                self::assertEquals(Request::PATCH, $response->request->getIni('method'));
                self::assertTrue($response->request->hasEndCallback());
            }))->addOptions(array(
            'method' => Request::PATCH
        ))->send();
        $responses[] = Request::create()->patch(TEST_SERVER . '/dynamic/all_data.php', array(
            'callback' => function (Response $response) {
                self::assertEquals(Request::PATCH, $response->request->getIni('method'));
                self::assertTrue($response->request->hasEndCallback());
            }))->send();

        //test post
        $responses[] = Request::create()->post(TEST_SERVER . '/dynamic/all_data.php', array('data' => 'this_is_post_data'), array(
            'callback' => function (Response $response) {
                self::assertEquals(Request::POST, $response->request->getIni('method'));
                self::assertTrue($response->request->hasEndCallback());
                self::assertContains('this_is_post_data', $response->body);

            }))->send();

        //test delete
        $responses[] = Request::create()->delete(TEST_SERVER . '/dynamic/all_data.php', array(), array(
            //            'data' => 'data=this_is_post_data', //not work
            'callback' => function (Response $response) {
                self::assertEquals(Request::DELETE, $response->request->getIni('method'));
                self::assertTrue($response->request->hasEndCallback());
                self::assertNotContains('this_is_post_data', $response->body);

            }))->send();

        //test proxy ip
        $responses[] = Request::create()->get('http://test-proxy.local/dynamic/all_data.php', array(
                'ip' => WEB_SERVER_HOST,
                'port' => WEB_SERVER_PORT,
                'timeout' => 2,
                'callback' => function (Response $response) {
                    self::assertFalse($response->hasErrors());
                    self::assertTrue(strlen($response->body) > 0);
                }
            ))->send();

        //test head
        $response = Request::create()->head(TEST_SERVER . '/dynamic/all_data.php?head', array(
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
    function testForm(){
        $result = \MultiHttp\Request::create()->quickPost(TEST_SERVER . '/dynamic/all_data.php', [
            'field_a' => 'post_data',
            'file_a' => "@".__DIR__."/static/test_image.jpg",
        ]);
        $result = json_decode($result, 1);
        $this->assertEquals('post_data', $result['p']['field_a']);
        $this->assertNotEmpty($result['p']['file_a']);
    }
    function testUpload(){
        $result = \MultiHttp\Request::create()->upload(TEST_SERVER . '/dynamic/all_data.php', [
            'field_a' => 'post_data',
            'file_a' => "@".__DIR__."/static/test_image.jpg",
//            'file_a' => new \CURLFile(__DIR__."/static/test_image.jpg"),
        ])->send();
        $result = json_decode($result->body, 1);
        $this->assertEquals('post_data', $result['p']['field_a']);
        $this->assertNotEmpty($result['f']['file_a']['size']);
    }

}
