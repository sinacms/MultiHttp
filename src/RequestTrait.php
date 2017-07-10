<?php
/**
 *
 * @author  qiangjian@staff.sina.com.cn sunsky303@gmail.com
 * Date: 2017/6/16
 * Time: 10:02
 * @version $Id: $
 * @since 1.0
 * @copyright Sina Corp.
 */

namespace MultiHttp;

trait RequestTrait {
    protected $expectContentType = null;
    public function expectsJson(){
        $this->expectContentType = Mime::JSON;
    }
    public function expectsXml(){
        $this->expectContentType = Mime::XML;
    }

    protected function json(){

    }
    protected function unJson(){

    }

    protected function xml(){

    }
    protected function unXml(){

    }
}

