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

    function testFiles()
    {
        echo __METHOD__ , ' starting ', PHP_EOL;
        $files = array(
            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/404-error.jpg',
            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/404-error.jpg',
            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
////            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/404-error.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
////            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/404-error.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/404-error.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
////            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/404-error.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
////            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/404-error.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/front/20170621/EWeJ-fyhfxph6284203.jpg',
//            'http://n.sinaimg.cn/auto/transform/20170621/jzwO-fyhfxph5858922.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/Yyju-fyhfxph5657320.jpg',
//            'http://n.sinaimg.cn/default/8_img/uplaod/3933d981/20170621/404-error.jpg',
            );
        try{
            $mr = MultiRequest::create()->setDefaults(['timeout'=>5]);
            foreach ($files as $file){
                $mr->add('GET', $file, null);
            }
            $rst = $mr->sendAll();
        }catch(\Exception $e){
            var_dump($e->getMessage());
        }
        $imgs = [];
        foreach($rst as $item){
            $base = base64_encode(($item->body) );
            echo "\n ". $item->request->uri . " \n body size: ". strlen($base);
            $imgs[]= [
                'data'=>['img_data'=> $base],
            ] ;
        }
        echo 'file size:'. sizeof($rst).PHP_EOL;
        try{
            $rst = MultiRequest::create()->setDefaults([
                'timeout'=>3,
                'method'=>'POST',
                'url' => 'http://test.learn.pub.sina.com.cn:8181/'
            ])->addOptions($imgs)->sendAll();
        }catch(\Exception $e){
            var_dump($e->getMessage());
        }
        if(is_array($rst)){
            foreach($rst as   $item){
                echo  " \n ". print_r([$item->body, $item->error, $item->code, $item->duration], 1);
            }
        }
        echo __METHOD__ , ' end ', PHP_EOL;
    }

    function test()
    {
        die;
        return false;
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

                        self::assertTrue(is_array($response->header) && sizeof($response->header) > 0);
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
<<<<<<< HEAD
                        self::assertEquals(true, strlen($response->body) > 0);
                        self::assertJson($response->body);
=======
                        self::assertNotEmpty($response->body);
>>>>>>> 1799bc479182f58431deae9f8975bf80f1e7cee6
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
<<<<<<< HEAD
                        self::assertEquals(true, strlen($response->body) > 0);
                        self::assertJson($response->body);
=======
                        self::assertNotEmpty($response->body);
>>>>>>> 1799bc479182f58431deae9f8975bf80f1e7cee6
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
                        self::assertEquals(true, strlen($response->body) > 0);
                        //test setDefaults
                        self::assertEquals(3, $response->request->getIni('timeout'));

                        self::assertContains('http://www.qq.com', $response->request->uri);
                        self::assertTrue($response->request->hasEndCallback());
                    },
                ),
                array(
<<<<<<< HEAD
                    'url' => 'http://proxy.test/dynamic/blocking.php',
                    'ip' => WEB_SERVER_HOST,
=======
                    'url' => 'http://proxy.test/dynamic/all_data.php',
                    'ip' => WEB_SERVER_HOST ,
>>>>>>> 1799bc479182f58431deae9f8975bf80f1e7cee6
                    'port' => WEB_SERVER_PORT,
                    'timeout' => 0,//unlimited timeout
                    'callback' => function (Response $response) {
                        self::assertFalse($response->hasErrors());
                        self::assertNotEmpty($response->body);
                    }
                ),
                array(
<<<<<<< HEAD
                    'url' => TEST_SERVER . '/dynamic/blocking.php',
                    'expectsMime' => 'json',
                    'sendMime' => 'json',
                    'method' => 'POST',
                    'data' => array('aaa' => 'bbc'),
=======
                    'url' => TEST_SERVER.'/dynamic/all_data.php',
                    'expects_mime' => 'json',
                    'send_mime' => 'json',
                    'method' => 'POST',
                    'data' => array('aaa'=>'bbc2'),
>>>>>>> 1799bc479182f58431deae9f8975bf80f1e7cee6
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
<<<<<<< HEAD
                    'url' => TEST_SERVER . '/dynamic/blocking.php',
//                    'expectsMime' => 'json',
//                    'sendMime' => 'json',
=======
                    'url' => TEST_SERVER.'/dynamic/all_data.php',
>>>>>>> 1799bc479182f58431deae9f8975bf80f1e7cee6
                    'method' => 'POST',
                    'data' => array('aaa' => 'bbc'),
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
