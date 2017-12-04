<?php
/**
 *
 * @author  qiangjian@staff.sina.com.cn sunsky303@gmail.com
 * Date: 2017/11/14
 * Time: 11:16
 * @version $Id: $
 * @since 1.0
 * @copyright Sina Corp.
 */

namespace MultiHttp;


class Helper
{
    public static function retry($maxTimes = 2, callable $task, $sleep = 0){
        $tryTimes = 0;
        while(++$tryTimes <= $maxTimes){
            if($task()) break;
            else usleep(abs($sleep) * 1000000);
        }
        return $tryTimes;
    }
}