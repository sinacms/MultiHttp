<?php
namespace MultiCurlTest;
/**
 *
 * @author  qiangjian@staff.sina.com.cn sunsky303@gmail.com
 * Date: 2017/6/15
 * Time: 18:31
 * @version $Id: $
 * @since 1.0
 * @copyright Sina Corp.
 */
class CurlTest extends \PHPUnit_Framework_TestCase
{
    private  $inst ;

    public static function setupBeforeClass()
    {

    }

    protected function setUp()
    {
        parent::setUp();
    }


    function testGet(){
        var_dump(CURLOPT_TIMEOUT, CURLOPT_TIMEOUT_MS);

        $t = microtime(1);
        $request = \sinacms\MultiHttp\Curl::create()->get('http://www.facebook.com', ['m'=>'get'], ['timeout'=>3, 'timeout_ms'=>2000]);
        $return = $request->fetch();
        var_dump($request->errorString());
        $tdiff = microtime(1)-$t;
        $this->assertTrue($tdiff > 2);
        $this->assertTrue($tdiff < 3);
        $t = microtime(1);
        $return =  \sinacms\MultiHttp\Curl::create()->get('http://www.facebook.com', ['m'=>'get'], [ 'timeout_ms'=>2000,'timeout'=>3,])->fetch();
        $tdiff = microtime(1)-$t;
        $this->assertTrue($tdiff > 3);
        $return =  \sinacms\MultiHttp\Curl::create()->get('http://qq.com', ['m'=>'get'], ['timeout'=>3])->fetch();
//        var_dump($return);
        $return =  \sinacms\MultiHttp\Curl::create()->get('http://www.baidu.com', ['m'=>'get'], ['timeout'=>1])->fetch();

    }
}