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
class MultiRequestTest extends \PHPUnit_Framework_TestCase
{
    public static function setupBeforeClass()
    {
    }

    function test()
    {
        $start = microtime(1);
        $mc = \MultiHttp\MultiRequest::create();
        $rtn = $mc->addOptions(
            array(
                array(
                    'url' => TEST_SERVER . '/dynamic/blocking.php?&1',
                    'timeout' => 2,
                    'method' => 'HEAD',
                    'data' => array(
                    ),
                    'callback' => function (Response $response) {
                        $this->assertEquals(false, $response->body);
                        $this->assertTrue(is_array($response->header) && $response->header);
                        $this->assertLessThan(1.5, $response->duration);
                        $this->assertFalse($response->hasErrors(), $response->request->getURI() . $response->error);
                        $this->assertEquals(TEST_SERVER . '/dynamic/blocking.php?&1', $response->request->getURI());
                        $this->assertEquals(Request::HEAD, $response->request->getIni('method'));
                        $this->assertTrue($response->request->hasEndCallback());
                    }
                ),

                array(
                    'url' => TEST_SERVER . '/dynamic/blocking.php?sleepSecs=&a',
                    'timeout' => 2,
                    'data' => array(
                        'data' => 'this_is_post_data'
                    ),
                    'callback' => function (Response $response) {
                       $this->assertFalse($response->hasErrors(), $response->request->getURI() . $response->error);
                        $this->assertEquals(Request::GET, $response->request->getIni('method'));
                        $this->assertTrue($response->request->hasEndCallback());
                        $this->assertNotContains('this_is_post_data', $response->body);
                    }
                ),
                array(
                    'url' => TEST_SERVER . '/dynamic/blocking.php?&b',
                    'method' => 'POST',
                    'timeout' => 2,
                    'data' => array(
                        'data' => 'this_is_post_data'
                    ),
                    'callback' => function (Response $response) {
                        $this->assertFalse($response->hasErrors(), $response->request->getURI() . $response->error);
                        $this->assertEquals(TEST_SERVER . '/dynamic/blocking.php?&b', $response->request->getURI());
                        $this->assertEquals(Request::POST, $response->request->getIni('method'));
                        $this->assertTrue($response->request->hasEndCallback());
                        $this->assertContains('this_is_post_data', $response->body);
                    }
                ),
                array(
                    'url' => TEST_SERVER . '/static/test.json',
                    'timeout' => 2,
                    'callback' => function (Response $response) {
                        $this->assertFalse($response->hasErrors(), $response->request->getURI() . $response->error);
                        $this->assertEquals(TEST_SERVER . '/static/test.json', $response->request->getURI());
                        $this->assertTrue(strlen($response->body) > 0);
                        $this->assertJsonStringEqualsJsonFile(WEB_SERVER_DOCROOT . '/static/test.json', $response->body);
                    }
                ),
                array(
                    'url' => 'http://www.qq.com',
                    'timeout' => 3,
                    'callback' => function (Response $response) {
                        $this->assertContains('http://www.qq.com', $response->request->getURI());
                        $this->assertTrue($response->request->hasEndCallback());
                    },
                ),
                array(
                    'url' => 'http://www.proxy.com',
                    'ip' => '127.0.0.1',
                    'timeout' => 1,
                    'callback' => function (Response $response) {
                var_dump($response->body);
                        $this->assertTrue($response->hasErrors());
                        $this->assertFalse(strlen($response->body) > 0);
                    }
                ),
            ))
            ->add(\MultiHttp\Http::GET, 'http://www.163.com', array(), array(
                'timeout' => 3,
                'callback' => function (Response $response) {
                    $this->assertContains('http://www.163.com', $response->request->getURI());
                },
            ))
            ->add('GET', 'http://sina.cn', array(), array(
                'timeout' => 3
            ))
            ->import(Request::create()->trace('http://sohu.cn', array(
                'timeout' => 3,
                'callback' => function (Response $response) {
                    $this->assertContains('http://sohu.cn', $response->request->getURI());
                }))->applyOptions())
            ->import(Request::create()->options('http://toutiao.com', array(
                'timeout' => 3,
                'callback' => function (Response $response) {
                    $this->assertContains('http://toutiao.com', $response->request->getURI());
                }))->applyOptions())
            ->execute();
        echo "exec done\n\t";
        foreach ($rtn as $response) {
            echo $response->request->getURI(), ' takes:', $response->duration, ' ', "\n\t\n\t";
        }
        $end = microtime(1);
        echo 'multi total takes:', $end - $start, ' secs;';
        $this->assertTrue($end - $start < 5);
    }

    protected function setUp()
    {
        parent::setUp();
        echo 'enter ' . __CLASS__ . PHP_EOL;
    }

}
