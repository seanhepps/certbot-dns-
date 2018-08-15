<?php
/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2018-07-06 13:53:03
 * @version $Id$
 */
class Aliyun implements DnsAlterInterface
{
    private $domain = 'https://alidns.aliyuncs.com/';
    private $parameter = array();
    private $key = 'LTAItEVgA2NcDKpv';
    private $secret = '1qfMwX05I8Hor9S4WLeq4gEZGHvAs2';
    // 初始化公共参数
    public function __construct()
    {
        // 公共参数
        $this->parameter['Format'] = 'Json';
        $this->parameter['Version'] = '2015-01-09';
        $this->parameter['AccessKeyId'] = $this->key;
        $this->parameter['SignatureMethod'] = 'HMAC-SHA1';
        $this->parameter['SignatureVersion'] = '1.0';
    }
    /**
     * 添加一个解析
     * @Author   Sean
     * @DateTime 2018-07-06
     * @param    [type]     $domain [解析名称]
     * @param    [type]     $value  [解析值]
     */
    public function addRecord($domain, $value, $dnsInfo)
    {
        $rootDomain = c('domain.' . str_replace('.', '_', $domain) . '.rootDomain');
        // 添加域名解析记录
        $this->parameter['Action'] = 'AddDomainRecord';
        $this->parameter['DomainName'] = $rootDomain?:$domain;
        $this->parameter['Type'] = 'TXT';
        $this->parameter['Value'] = $value;
        $this->parameter['RR'] = $rootDomain ? '_acme-challenge.' . str_replace('.'.$rootDomain, '', $domain) : '_acme-challenge';
        // 生成参数
        $composeUrl = $this->composeUrl($domain, $value);
        // 请求
        $request = new Request();
        $result = $request->open($composeUrl)->send();
        $result = json_decode($result, true);
        if (isset($result['Message'])) {
            Debug::log($domain . ':' . $result['Message']);
        } else {
            // 保存recordId
            recordId($domain, $result['RecordId']);
        }
        sleep(1);
    }
    /**
     * 删除一个解析
     * @Author   Sean
     * @DateTime 2018-07-06
     * @param    [type]     $domain  [解析名称]
     * @param    [type]     $value   [解析值]
     * @param    [type]     $dnsInfo [dnsInfo 包含key]
     * @return   [type]              [description]
     */
    public function deleteRecord($domain, $value, $dnsInfo)
    {
        $this->parameter['Action'] = 'DeleteDomainRecord';
        $this->parameter['RecordId'] = recordId($domain);
        // 生成参数
        $composeUrl = $this->composeUrl($domain, $value);
        // 请求
        $request = new Request();
        $result = $request->open($composeUrl)->send();
        $result = json_decode($result, true);
        
        if (isset($result['Message'])) {
            Debug::log($domain . ': ' . $result['Message']);
        } else {
            // 删除recordId
            recordId($domain, null);
        }
    }
    /**
     * 部署到aliyun的cdn上面
     * @Author   Sean
     * @DateTime 2018-08-07
     * @param    [type]     $domain  [description]
     * @param    [type]     $value   [description]
     * @param    [type]     $dnsInfo [description]
     * @return   [type]              [description]
     */
    public function deploy($domain, $value, $dnsInfo)
    {
        $this->domain = 'https://cdn.aliyuncs.com/';
        $this->parameter['Version'] = '2014-11-11';
        $this->parameter['Action'] = 'SetDomainServerCertificate';
        $this->parameter['ServerCertificateStatus'] = 'on';
        // 证书
        $this->parameter['ServerCertificate'] = file_get_contents($_SERVER['RENEWED_LINEAGE'] . '/fullchain.pem');
        $this->parameter['PrivateKey'] = file_get_contents($_SERVER['RENEWED_LINEAGE'] . '/privkey.pem');
        // 域名
        $domains = empty($_SERVER['RENEWED_DOMAINS']) ? array($domain) : explode(' ', $_SERVER['RENEWED_DOMAINS']);
        foreach ($domains as $rd) {
            // 如果开头是星就去掉 *.
            if ($rd[0] == '*') {
                $rd = substr($rd, 2);
            }
            $_domains = (array)c('domain.' . str_replace('.', '_', $rd) . '.cdnName');
            if (!$_domains) continue;
            // 如果该域名证书时通配符 则可能出现布置在多个cdn上面
            foreach ($_domains as $d) {

                $this->parameter['CertName'] = date('Ymdhis') . $d;
                $this->parameter['DomainName'] = $d;
                unset($this->parameter['Signature']);
                // 生成参数
                $composeUrl = $this->composeUrl($d, $value, $dnsInfo);
                // 请求
                $request = new Request();
                $result = $request->open($composeUrl)->send();
                $result = json_decode($result, true);
                // 有时候成功时也返回网络错误
                if (isset($result['Message']) && $result['Code'] != 'InternalError') {
                    Debug::log($d . ': ' . $result['Message']);
                }
            }
        }
    }
    /**
     * 构建参数
     * @Author   Sean
     * @DateTime 2018-07-23
     * @param    [type]     $domain [description]
     * @param    [type]     $value  [description]
     * @return   [type]             [description]
     */
    private function composeUrl($domain,$value, $dnsInfo)
    {
        // 时间
        $this->parameter['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
        // 随机数
        $this->parameter['SignatureNonce'] = md5(uniqid(mt_rand(), true));
        // 签名
        $this->parameter['Signature'] = $this->computeSignature($this->parameter, $dnsInfo);
        // 请求字符串
        $parameters = $this->parameter;
        $requestUrl = $this->domain . '?';
        foreach ($parameters as $key => $value) {
            $requestUrl .= $key . "=" . urlencode($value) . "&";
        }
        return substr($requestUrl, 0, -1);
    }
    /**
     * 签名
     * @Author   Sean
     * @DateTime 2018-07-23
     * @param    [type]     $str    [请求字符串]
     * @return   [type]             [description]
     */
    public function computeSignature($parameters, $dnsInfo)
    {
        ksort($parameters);
        $canonicalizedQueryString = '';
        foreach ($parameters as $key => $value) {
            $canonicalizedQueryString .= '&' . $this->percentEncode($key). '=' . $this->percentEncode($value);
        }
        $stringToSign = 'GET&%2F&' . $this->percentEncode(substr($canonicalizedQueryString, 1));
        return sha1_hmac($stringToSign, c('dns.' . $dnsInfo['dns'] . '.secret') . '&');
    }
    /**
     * 对请求字符串进行编码处理
     * @Author   Sean
     * @DateTime 2018-07-23
     * @param    [type]     $str [description]
     * @return   [type]          [description]
     */
    public function percentEncode($str)
    {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }
}