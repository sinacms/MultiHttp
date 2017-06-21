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
    protected function setUp()
    {
        parent::setUp();
        echo 'enter '.__CLASS__.PHP_EOL;
    }
    function test()
    {
        $start = microtime(1);

        $mc = \MultiHttp\MultiRequest::create();
        $rtn = $mc->addOptions(
            [
                [
                    'url' => TEST_SERVER . '/dynamic/blocking.php?sleepSecs=1',
                    'timeout' => 2,
                    'data' => [
                        'data' => 'this_is_post_data'
                    ],
                    'callback' => function (Response $response) {
                        #$this->assertLessThan(3, $response->duration);
                        #$this->assertGreaterThan(1, $response->duration);
                        $this->assertFalse($response->hasErrors(),$response->request->getURI() . $response->error);
                        $this->assertEquals(TEST_SERVER . '/dynamic/blocking.php?sleepSecs=1', $response->request->getURI());
                        $this->assertEquals(Request::GET, $response->request->getIni('method'));
                        $this->assertTrue($response->request->hasEndCallback());
                        $this->assertTrue($response->request->hasInitialized());
                        $this->assertNotContains('this_is_post_data', $response->body);
                    }
                ],
                [
                    'url' => TEST_SERVER . '/dynamic/blocking.php?sleepSecs=1',
                    'method' => 'POST',
                    'timeout' => 2,
                    'data' => [
                        'data' => 'this_is_post_data'
                    ],
                    'callback' => function (Response $response) {
                        $this->assertLessThan(3, $response->duration);
                        $this->assertGreaterThan(1, $response->duration);
                        #$this->assertFalse($response->hasErrors(),$response->request->getURI() . $response->error);
                        $this->assertEquals(TEST_SERVER . '/dynamic/blocking.php?sleepSecs=1', $response->request->getURI());
                        $this->assertEquals(Request::POST, $response->request->getIni('method'));
                        $this->assertTrue($response->request->hasEndCallback());
                        $this->assertTrue($response->request->hasInitialized());
                        $this->assertContains('this_is_post_data', $response->body);
                    }
                ],
                [
                    'url' => TEST_SERVER . '/static/test.json',
                    'timeout' => 2,
                    'callback' => function (Response $response) {
                        $this->assertFalse($response->hasErrors(),$response->request->getURI() . $response->error );
                        $this->assertEquals(TEST_SERVER . '/static/test.json', $response->request->getURI());
                        $this->assertTrue(strlen($response->body) > 0);
                        $this->assertJsonStringEqualsJsonFile(WEB_SERVER_DOCROOT . '/static/test.json', $response->body);
                    }
                ],
                [
                    'url' => 'http://www.qq.com',
                    'timeout' => 3,
                    'callback' => function (Response $response) {
                        $this->assertContains('http://www.qq.com', $response->request->getUri());
                        $this->assertTrue($response->request->hasEndCallback());
                        $this->assertTrue($response->request->hasInitialized());
                    },
                ],
                [
                    'url' => 'http://www.facebook.com',
                    'timeout' => 3,
                    'callback' => function (Response $response) {
                        $this->assertTrue($response->hasErrors());
                        $this->assertFalse(strlen($response->body) > 0);
                    }
                ],
                [
                    'url' => 'http://www.proxy.com',
                    'ip' => '127.0.0.1',
                    'timeout' => 3,
                    'callback' => function (Response $response) {
                var_dump($response->info);
//                        $this->assertTrue($response->info['']);
                        $this->assertTrue($response->hasErrors());
                        $this->assertFalse(strlen($response->body) > 0);
                    }
                ],
            ])
            ->add(\MultiHttp\Http::GET, 'http://www.163.com', [], [
                'timeout' => 3,
                'callback' => function (Response $response) {
                    $this->assertContains('http://www.163.com', $response->request->getUri());
                },
            ])
            ->add('GET', 'http://sina.cn', [
                'timeout' => 3
            ])
            ->onBeginning(function () {
                $n = 3;
                echo 'Concurrent start sleep ' . $n . 's ' . PHP_EOL;
//                sleep($n);
                echo 'Concurrent end' . PHP_EOL;
            })
            ->import(Request::create()->trace('http://sohu.cn', [
                'timeout' => 3,
                'callback' => function (Response $response) {
                    $this->assertContains('http://sohu.com', $response->request->getUri());
                }])->applyOptions())
            ->import(Request::create()->options('http://toutiao.com', [
                'timeout' => 3,
                'callback' => function (Response $response) {
                    $this->assertContains('http://toutiao.com', $response->request->getUri());
                }])->applyOptions())
            ->execute();
        echo "exec done\n\t";
        foreach ($rtn as $response) {
            echo $response->request->getUri(), ' takes:', $response->duration, "\n\t\n\t";
        }
        $end = microtime(1);
        echo 'multi total takes:', $end - $start, ' secs;';
        $this->assertTrue($end - $start < 4);
    }


}
