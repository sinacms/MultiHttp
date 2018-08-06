<?php

namespace MultiCurlTest;

use MultiHttp\Http;
use MultiHttp\Mime;
use MultiHttp\Request;
use MultiHttp\Response;


class MimeTest extends \PHPUnit_Framework_TestCase
{
    protected $mime;
    protected function setUp()
    {
        $this->mime  = new Mime();
        parent::setUp();
    }
   function testSupportsMimeType(){
        $this->assertTrue(Mime::supportsMimeType('json'));
        $this->assertTrue(Mime::supportsMimeType('xml'));
        $this->assertTrue(Mime::supportsMimeType('form'));
        $this->assertTrue(Mime::supportsMimeType('upload'));
        $this->assertTrue(Mime::supportsMimeType('plain'));
        $this->assertTrue(Mime::supportsMimeType('text'));
        $this->assertTrue(Mime::supportsMimeType('html'));
        $this->assertTrue(Mime::supportsMimeType('xml'));
   }

}
