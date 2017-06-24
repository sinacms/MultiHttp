# MultiHttp
    
![icon](https://api.travis-ci.org/sinacms/MultiHttp.svg?branch=master)
![icon](https://scrutinizer-ci.com/g/sinacms/MultiHttp/badges/quality-score.png?b=master)
![icon](https://scrutinizer-ci.com/g/sinacms/MultiHttp/badges/coverage.png?b=master)
    
    
    This is high performance PHP curl wrapper written in PHP.
	It's compatible with PHP 5.3+ .

    
## Feature
 - alias of curl option, e.g.  'timeout' equals 'CURLOPT_TIMEOUT' etc.
 - Request  and  MultiRequest class  ,  can be used in any combination 
 - graceful and efficient

## Installation
   
   You can use composer to install this library from the command line.
```bash
composer install
```   

   
## Usage

### Single cURL:


```php
<?php
// Include Composer's autoload file if not already included.
require '../vendor/autoload.php'; 
$responses=array();
$responses[] = Request::create()->addQuery('wd=good')->get('http://baidu.com?', array(
          'timeout' => 3,
          'timeout_ms' => 2000,
          'callback' => function (Response $response) {

          }))->execute();

$responses[] = Request::create()->get('http://qq.com', array(
          'callback' => function (Response $response) {
              //todo
          }))->addOptions(array(
          'method' => Request::PATCH,
          'timeout' => 3,
      ))->execute();
      //test post
$responses[] = Request::create()->post(
      'http://127.0.0.1',array('data'=>'this_is_post_data'), array(
          'callback' => function (Response $response) {
              //todo
          }))->execute();

foreach ($responses as $response) {
  echo $response->request->getURI(), ' takes:', $response->duration,  "\n\t\n\t";
}      
``` 


 Multi cURL:
 
```php
<?php
require '../vendor/autoload.php'; 
$mc  = \MultiHttp\MultiRequest::create();
$rtn = $mc->addOptions(
    array(
        array(
            'url'    => 'http://google.com',
            'timeout' => 2,
            'method' => 'HEAD',
            'data'   => array(
            ),
            'callback' => function (Response $response) {
                //todo
            }
        ),
    ))
    ->add('GET', 'http://sina.cn',array(), array(
        'timeout' => 3
    ))
    ->import(Request::create()->trace('http://sohu.cn', array(
            'timeout'  => 3,
            'callback' => function (Response $response) {
                //todo
            }))->applyOptions())
	->execute();
    foreach ($rtn as $response) {
        echo $response->request->getURI(), ' takes:', $response->duration, ' ', "\n\t\n\t";
    }

``` 
 
