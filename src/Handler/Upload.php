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

namespace MultiHttp\Handler;

/**
 * Trait JsonTrait
 * @package MultiHttp
 */
class Upload implements IHandler
{
    /**
     * @param $body
     * @return string
     */
    public function encode($body)
    {
        return $body;
    }

    /**
     * @param $body
     * @return mixed
     */
    public function decode($body)
    {
        return $body;
    }
}

