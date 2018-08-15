<?php
/**
 *
 * @authors Your Name (you@example.org)
 * @date    2018-07-06 13:00:45
 * @version $Id$
 */
class Request
{
    protected $ch = null;
    // 请求方法
    protected $method = 'get';
    // 请求后的信息
    protected $info = null;

    public function open($url = '')
    {
        $this->ch = curl_init($url);
        $set      = array(
            CURLOPT_RETURNTRANSFER    => 1,     // 返回返回结果而不是直接输出
            CURLOPT_TIMEOUT           => 30,
            CURLOPT_SSL_VERIFYPEER    => false, // 不进行ssl验证
            CURLOPT_SSL_VERIFYHOST    => false,
            CURLOPT_FOLLOWLOCATION    => true,  // 允许重定向
            CURLOPT_MAXREDIRS         => 5,     // 重定向最大次数
            CURLOPT_CONNECTTIMEOUT    => 5,
            CURLOPT_CONNECTTIMEOUT_MS => 5000,  // 链接等待时间
        );
        curl_setopt_array($this->ch, $set);
        return $this;
    }
    public function method($method = 'get')
    {
        $method = strtoupper($method);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        $this->method = $method;
        return $this;
    }
    /**
     * 设置curl选项
     * @param  array  $op [option=>value]
     * @return [type]     [description]
     */
    public function option(array $op)
    {
        curl_setopt_array($this->ch, $op);
        return $this;
    }
    /**
     * 请求
     * @Author   Sean
     * @DateTime 2018-07-06
     * @param    array      $data [请求的结果]
     * @return   [type]           [description]
     */
    public function send($data = array())
    {
        if ($data) {
            if (is_array($data)) {
                $data = http_build_query($data);
            }
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        }
        // 执行
        if (curl_exec($this->ch) === false) {
            // throw new \Exception( curl_error( $ch ) );
            $data = '';
        } else {
            $data = curl_multi_getcontent($this->ch);
        }
        $this->info = curl_getinfo($this->ch);

        return $data;
    }
    public function getInfo()
    {
        return $this->info;
    }
}
