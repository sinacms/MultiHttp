# MultiHttp
    
![icon](https://api.travis-ci.org/sinacms/MultiHttp.svg?branch=master)
    This is high performance PHP curl wrapper written in php.
    
    
    
## Installation
   
   You can use composer to install this library from the command line.
   
   composer install
   
## Usage

### Single cURL:


```
<?php
// Include Composer's autoload file if not already included.
require '../vendor/autoload.php'; 
$responses=[];
$responses[] = Request::create()->addQuery('wd=good')->get('http://baidu.com?', [
          'timeout' => 3,
          'timeout_ms' => 2000,
          'callback' => function (Response $response) {

          }])->execute();

$responses[] = Request::create()->get('http://qq.com', [
          'callback' => function (Response $response) {
              //todo
          }])->addOptions([
          'method' => Request::PATCH
      ])->execute();
      //test post
$responses[] = Request::create()->post(
      'http://127.0.0.1',['data'=>'this_is_post_data'], [
          'callback' => function (Response $response) {
              //todo
          }])->execute();

foreach ($responses as $response) {
  echo $response->request->getUri(), ' takes:', $response->duration,  "\n\t\n\t";
}      
``` 

 Multi cURL:
 
```
require '../vendor/autoload.php'; 
$mc  = \MultiHttp\MultiRequest::create();
$rtn = $mc->addOptions(
    [
        [
            'url'    => 'http://google.com',
            'timeout' => 2,
            'method' => 'HEAD',
            'data'   => [
            ],
            'callback' => function (Response $response) {
                //todo
            }
        ],
    ])
    ->add('GET', 'http://sina.cn',[], [
        'timeout' => 3
    ])
    ->import(Request::create()->trace('http://sohu.cn', [
            'timeout'  => 3,
            'callback' => function (Response $response) {
                //todo
            }])->applyOptions())
	->execute();
    foreach ($rtn as $response) {
        echo $response->request->getUri(), ' takes:', $response->duration, ' ', "\n\t\n\t";
    }

``` 
 