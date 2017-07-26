# MultiHttp

[![](https://api.travis-ci.org/sinacms/MultiHttp.svg?branch=master)](https://travis-ci.org/sinacms/MultiHttp)
[![](https://scrutinizer-ci.com/g/sinacms/MultiHttp/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sinacms/MultiHttp)
[![](https://scrutinizer-ci.com/g/sinacms/MultiHttp/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/sinacms/MultiHttp/)
    
    
    This is high performance PHP curl wrapper written in PHP.
	It's compatible with PHP 5.4+ and HHVM .
	Notice that libcurl version must be over 7.36.0, libcurl version must be over 7.36.0， otherwise timeout can not suppert decimal.


## Contents

 * [Feature](#feature)
 * [Installation](#installation)
 * [Usage](#usage)
   * [Single-request](single-request)
   * [Multi-request](multi-request)
 * [Documentation](#documentation)
   * [Request](#request)
   * [MultiRequest](#multiRequest)
 



    
## Feature
 - alias of curl option, e.g.  'timeout' equals 'CURLOPT_TIMEOUT' etc.
 - Request  and  MultiRequest class  ,  can be used in any combination 
 - graceful and efficient

## Installation

   You can use composer to install this library from the command line.
```bash
composer require sinacms/multihttp
```   

   
## Usage

### Single-request:


```php
<?php
// Include Composer's autoload file if not already included.
require __DIR__.'/vendor/autoload.php';
use MultiHttp\Request;
use MultiHttp\Response;


$responses=array();
$responses[] = Request::create()->addQuery('wd=good')->get('http://baidu.com?', array(
          'timeout' => 3,
          'timeout_ms' => 2000,
          'callback' => function (Response $response) {

          }))->send();

$responses[] = Request::create()->get('http://qq.com', array(
          'callback' => function (Response $response) {
              //sth
          }))->addOptions(array(
          'method' => Request::PATCH,
          'timeout' => 3,
      ))->send();
      //test post
$responses[] = Request::create()->post(
      'http://127.0.0.1',array('data'=>'this_is_post_data'), array(
          'callback' => function (Response $response) {
              //sth
          }))->send();

foreach ($responses as $response) {
  echo $response->request->uri, ' takes:', $response->duration,  "\n\t\n\t";
}
?>
``` 


### Multi-request:
 
```php
<?php
use MultiHttp\MultiRequest;

$mr  = MultiRequest::create();
$rtn = $mr->addOptions(
    array(
        array(
            'url'    => 'http://google.com',
            'timeout' => 2,
            'method' => 'HEAD',
            'data'   => array(
            ),
            'callback' => function (Response $response) {
                //sth
            }
        ),
    ))
    ->add('GET', 'http://sina.cn',array(), array(
        'timeout' => 3
    ))
    ->import(Request::create()->trace('http://sohu.cn', array(
            'timeout'  => 3,
            'callback' => function (Response $response) {
                //sth
            }))->applyOptions())
	->send();
    foreach ($rtn as $response) {
        echo $response->request->uri, ' takes:', $response->duration, ' ', "\n\t\n\t";
    }

?>
``` 

## Documentation 
  * ### Request
   * #### option shorthand
    		'url'             => 'CURLOPT_URL',
    		'debug'           => 'CURLOPT_VERBOSE',//for debug verbose
    		'method'          => 'CURLOPT_CUSTOMREQUEST',
    		'data'            => 'CURLOPT_POSTFIELDS', // array or string , file begin with '@'
    		'ua'              => 'CURLOPT_USERAGENT',
    		'timeout'         => 'CURLOPT_TIMEOUT', // (secs) 0 means indefinitely
    		'connect_timeout' => 'CURLOPT_CONNECTTIMEOUT',
    		'referer'         => 'CURLOPT_REFERER',
    		'binary'          => 'CURLOPT_BINARYTRANSFER',
    		'port'            => 'CURLOPT_PORT',
    		'header'          => 'CURLOPT_HEADER', // TRUE:include header
    		'headers'         => 'CURLOPT_HTTPHEADER', // array
    		'download'        => 'CURLOPT_FILE', // writing file stream (using fopen()), default is STDOUT
    		'upload'          => 'CURLOPT_INFILE', // reading file stream
    		'transfer'        => 'CURLOPT_RETURNTRANSFER', // TRUE:return string; FALSE:output directly (curl_exec)
    		'follow_location' => 'CURLOPT_FOLLOWLOCATION',
    		'timeout_ms'      => 'CURLOPT_TIMEOUT_MS', // milliseconds,  libcurl version > 7.36.0 ,
   
   * public static function create()
   * public function endCallback()
   * public function hasEndCallback()
   * public function onEnd(callable$callback)
   * public function uri
   * public function getIni($field)
   * public function addQuery($data)
   * public function post($uri, array $payload = array(), array $options = array())
   * public function addOptions(array $options = array())
   * public function get($uri, array $options = array())
   * public function send()
   * public function applyOptions()
   * public function makeResponse($isMultiCurl = false)
  * ### MultiRequest
   * public static function create()
   * public function addOptions(array $URLOptions)
   * public function add($method, $uri, array $payload = array(), array $options = array())
   * public function import(Request $request)
   * public function sendAll()


   [More][https://github.com/sinacms/MultiHttp/blob/master/usage.md](https://github.com/sinacms/MultiHttp/blob/master/usage.md)