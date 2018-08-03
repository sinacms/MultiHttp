<?php
/**
 * Created by IntelliJ IDEA.
 * User: qiangjian
 * Date: 2018/8/3
 * Time: 15:53
 */

namespace MultiHttp\Handler;


interface IHandler
{
    public function encode($body);
    public function decode($body);
}