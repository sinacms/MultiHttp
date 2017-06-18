<?php
namespace MultiCurl;

/**
 *
 * @author  qiangjian@staff.sina.com.cn sunsky303@gmail.com
 * Date: 2017/6/15
 * Time: 18:31
 * @version $Id: $
 * @since 1.0
 * @copyright Sina Corp.
 */
class MultiCurlTest extends \PHPUnit_Framework_TestCase
{
    private  $inst ;

    public static function setupBeforeClass()
    {

    }

    protected function setUp()
    {
        parent::setUp();
        $this->inst = new \MultiCurl();
    }


    function testAddUrlOptions(){

    }
}