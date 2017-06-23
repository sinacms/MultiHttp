<?php
/**
 *
 * @author  qiangjian@staff.sina.com.cn sunsky303@gmail.com
 * Date: 2017/6/19
 * Time: 10:55
 * @version $Id: $
 * @since 1.0
 * @copyright Sina Corp.
 */

namespace MultiHttp;



class Response
{
    public
        $code,
        $errorCode,
        $error,
        $header,
        $body,
        /**
         * @var Request
         */
        $request,
        $contentType,
        $charset,
        $duration,
        $info;
    protected function __construct()
    {
    }

    public static function create(Request $request, $body, $info, $errorCode, $error){
        $self = new self;
        $self->request = $request;
        $self->body = $body;
        $self->info = $info;
        $self->errorCode = $errorCode;
        $self->error = $error;
        $self->parse();
        $self->check();
        return $self;
    }
    public function check(){

    }
    public function parse(){
        if($this->body && $this->request->getIni('header')){//has header
            $headers = rtrim(substr($this->body, 0, $this->info['header_size']));
            $this->body = substr($this->body, $this->info['header_size']);
            $headers = explode(PHP_EOL, $headers);
            array_shift($headers); // HTTP HEADER
            foreach($headers as $h) {
                if(false !== strpos($h, ':'))
                    list($k, $v) = explode(':', $h, 2);
                else
                    list($k, $v) = array($h,'');

                $this->header[trim($k)] = trim($v);
            }
        }
        $this->code = $this->info['http_code'];
        $this->duration = $this->info['total_time'];
        $this->contentType = $this->info['content_type'];
        $content_type = isset($this->info['content_type']) ? $this->info['content_type'] : '';
        $content_type = explode(';', $content_type);
        $this->contentType = $content_type[0];
        if (count($content_type) == 2 && strpos($content_type[1], '=') !== false) {
            list( , $this->charset) = explode('=', $content_type[1]);
        }
    }

    /**
     * Status Code Definitions
     *
     * Informational 1xx
     * Successful    2xx
     * Redirection   3xx
     * Client Error  4xx
     * Server Error  5xx
     *
     * http://pretty-rfc.herokuapp.com/RFC2616#status.codes
     *
     * @return bool Did we receive a 4xx or 5xx?
     */
    public function hasErrors()
    {
        return $this->code == 0 || $this->code >= 400;
    }

}