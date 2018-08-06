<?php

namespace MultiCurlTest;

use MultiHttp\Http;
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
class HttpMocked extends Http{
     function post($uri, $payload = null, array $options = array()){}

     function patch($uri, $payload = null, array $options = array()){}

     function put($uri, $payload = null, array $options = array()){}
    
     function get($uri, array $options = array()){}
    
     function head($uri, array $options = array()){}

     function delete($uri, array $options = array()){}

     function options($uri, array $options = array()){}

     function trace($uri, array $options = array()){}

}
class HttpTest extends \PHPUnit_Framework_TestCase
{
    protected $http;
    protected function setUp()
    {
        $this->http  = new HttpMocked();
        parent::setUp();
    }
   function testHasBody(){
        $this->assertTrue(HttpMocked::needBody('POST'));
        $this->assertTrue(HttpMocked::needBody('PUT'));
        $this->assertTrue(HttpMocked::needBody('PATCH'));
        $this->assertTrue(HttpMocked::needBody('OPTIONS'));
   }

}
