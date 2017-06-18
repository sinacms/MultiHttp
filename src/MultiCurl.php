<?php
namespace sinacms\MultiHttp;

/**
 *
 * @author  qiangjian@staff.sina.com.cn sunsky303@gmail.com
 * Date: 2017/6/9
 * Time: 15:09
 * @version $Id: $
 * @since 1.0
 * @copyright Sina Corp.
 */
class MultiCurl
{
    protected static $multiHandler;
    protected $urlAndOptions;

    public function __construct(array $urlAndOptions)
    {
        $this->urlAndOptions = $urlAndOptions;
    }

    /**
     * @param array $URLOptions example: ['http://localhost:9999/' => ['timeout'=>1, 'method'=>'POST', 'post'=>'aa=bb&c=d'],]
     * @return $this
     */
    public function addGet($url, array $option = [])
    {
        $this->add($url, 'GET', $option);
        return $this;
    }

    public function addPost($url, array $option = [])
    {
        $this->add($url, 'POST', $option);
        return $this;
    }

    public function setDefault($option = [])
    {
        foreach ($this->urlAndOptions as &$item) {
            if (isset($item['url']) && sizeof($item) == 1) $item = $option;
        }
    }



    protected function prepare()
    {
        if (empty($this->urlAndOptions)) throw  new \InvalidArgumentException('url and options can not be empty');
        foreach ($this->urlAndOptions as &$item) {
            self::filter($item);
        }
        self::$multiHandler = curl_multi_init();
        return $this;
    }

    protected function setup()
    {
        foreach ($this->urlAndOptions as $opt) {
            $ch = curl_init();
            if (isset($opt['timeout'])) curl_setopt($ch, CURLOPT_TIMEOUT, $opt['timeout']);
            if (isset($opt['method'])) curl_setopt($ch, CURLOPT_POST, $opt['method'] == 'POST');
            if (isset($opt['post'])) curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($opt['post']) ? http_build_query($opt['post']) : $opt['post']);
            if (!isset($opt['url'])) throw Exception('url not exists');
            $url = $opt['url'];
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle(self::$multiHandler, $ch);
            self::$urlPool[] = [$url => $ch];
        }
    }

    public function execute()
    {
        $running = null;
        while (CURLM_CALL_MULTI_PERFORM == curl_multi_exec(self::$multiHandler, $running)) ;
//        do {
//            ;
//            usleep(50);
//        } while($running > 0);

        $return = [];
        foreach (self::$urlPool as $item) {
            $url = key($item);
            $ch = $item[$url];
            $return[] = [$url => curl_multi_getcontent($ch)];
            curl_multi_remove_handle(self::$multiHandler, $ch);
            curl_close($ch);
        }
        curl_multi_close(self::$multiHandler);

        return $return;
    }
}
