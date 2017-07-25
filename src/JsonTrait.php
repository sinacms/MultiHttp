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

use MultiHttp\Exception\UnexpectedResponseException;

/**
 * Trait JsonTrait
 * @package MultiHttp
 */
trait JsonTrait
{
    /**
     * @return Request
     */
    public function expectsJson()
    {
        return $this->expectsMime('json');
    }

    /**
     * @return Request
     */
    public function sendJson()
    {
        return $this->sendMime('json');
    }

    /**
     * @param $body
     * @return string
     */
    public function json($body)
    {
        return json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param $body
     * @return mixed
     */
    public function unJson($body)
    {
        $parsed = json_decode($body, true);
        if(json_last_error() !== JSON_ERROR_NONE)throw new UnexpectedResponseException('parsing json occurs error: '.  self::jsonLastErrorMsg() . ', raw body: ' .$body  );
        return $parsed;
    }

    /**
     * @return string
     */
    private static function jsonLastErrorMsg(){
        if(function_exists('json_last_error_msg')) return json_last_error_msg();
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return ' - No errors';
                break;
            case JSON_ERROR_DEPTH:
                return ' - Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                return ' - Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                return ' - Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                return ' - Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                return ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                return ' - Unknown error';
                break;
        }
    }
}

